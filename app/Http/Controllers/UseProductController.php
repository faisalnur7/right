<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UseProduct;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\SaleLog;
use App\Models\User;
use App\Models\UserSaleLogUnit;
use App\Models\BinaryTreeNode;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\BinaryTreeService;
use App\Services\CommissionDistributionService;

class UseProductController extends Controller
{
    public function stock_sale(Request $request)
    {
        $validated = $request->validate([
            'from_user_id' => 'nullable|exists:users,id',
            'user_id'      => 'required|exists:users,id',
            'product_id'   => 'required|exists:products,id',
            'sale_log_id'  => 'nullable|exists:sale_logs,id',
            'quantity'     => 'required|integer|min:1',
            'status'       => 'nullable|string',
            'remarks'      => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $authUserId = auth()->id();

        return DB::transaction(function () use ($validated, $product, $authUserId) {
            
            $user = User::find($validated['user_id']);
            $isReferrerExists = BinaryTreeNode::query()->where('user_id', $user->referenceUser?->id)->where('sale_log_id', $validated['sale_log_id'])->where('status', 1)->exists();
            if(!$isReferrerExists){
                session()->flash('error', 'Referrer is not active in the sale log.');
                return redirect()->back();
            }

            // ✅ 1. Fetch confirmed stock for authenticated user
            $stockLogs = OrderItem::whereHas('order', function ($query) use ($authUserId) {
                    $query->where('user_id', $authUserId)
                        ->where('status', Order::COMPLETED);
                })
                ->selectRaw('product_id, sale_log_id, SUM(quantity) as total_quantity')
                ->groupBy('product_id', 'sale_log_id')
                ->get();

            $totalStock = $stockLogs->where('product_id', $product->id)->sum('total_quantity');

            if ($totalStock < $validated['quantity']) {
                throw new \Exception('Insufficient stock for this product.');
            }

            // ✅ 2. Ensure `from_user_id` and `sale_log_id` are set
            $validated['from_user_id'] = $validated['from_user_id'] ?? $authUserId;

            if (empty($validated['sale_log_id'])) {
                $firstLog = $stockLogs->where('product_id', $product->id)->first();
                if (!$firstLog) {
                    throw new \Exception('No valid sale log found for this product.');
                }
                $validated['sale_log_id'] = $firstLog->sale_log_id;
            }

            $saleLog = $product->saleLogs->firstWhere('id', $validated['sale_log_id']);
            if (!$saleLog || !$saleLog->pivot) {
                throw new \Exception('Invalid or missing sale log for this product.');
            }
            // ✅ 3. Create use product record
            $useProduct = UseProduct::create([
                'user_id'       => $validated['user_id'],
                'from_user_id'  => $validated['from_user_id'],
                'product_id'    => $product->id,
                'sale_log_id'   => $validated['sale_log_id'],
                'quantity'      => $validated['quantity'],
                'price'         => $product->price,
                'total'         => $product->price * $validated['quantity'],
                'status'        => $validated['status'] ?? UseProduct::USED_PRODUCT,
                'used_at'       => now(),
                'remarks'       => $validated['remarks'] ?? null,
                'validity'      => $saleLog->pivot->unit,
                'unit_per_day'  => $saleLog->pivot->price,
            ]);

            // ✅ 4. Manage UserSaleLogUnit (activation tracking)
            $logUnit = UserSaleLogUnit::firstOrCreate([
                'user_id'     => $validated['user_id'],
                'sale_log_id' => $validated['sale_log_id'],
            ]);

            $logUnit->increment('remaining_units', $validated['quantity']);
            $logUnit->is_active = $logUnit->remaining_units > 0 ? 1 : 0;
            $logUnit->save();

            // ✅ 5. Insert user into binary tree
            $user = User::find($validated['user_id']);
            $isReferrerExists = BinaryTreeNode::query()->where('user_id', $user->referenceUser?->id)->where('sale_log_id', $validated['sale_log_id'])->where('status', 1)->exists();
            if(!$isReferrerExists){
                session()->flash('error', 'Referrer is not active in the sale log.');
                return redirect()->back();
            }

            $user     = User::find($validated['user_id']);
            $referrer = $user->referenceUser ?? User::find($validated['from_user_id']);
            $binaryTreeService = new BinaryTreeService();
            $node = $binaryTreeService->insertIntoBinaryTree($user, $validated['sale_log_id'], $referrer);

            // ✅ 5. Distribute Commission
            $commissionService = new CommissionDistributionService();
            $commissionService->distributeAllCommissions(
                $node,
                $user,
                $validated['sale_log_id'],
                $product->total_use_commission ?? 0,
                $product->associate_commission ?? 0
            );

            // ✅ 6. Commit transaction + success response
            session()->flash('success', 'Product sold successfully and user added to binary tree.');
            return redirect()->back();
        });
    }

    public function stock_sale_log()
    {
        $userId = auth()->id();

        // Get all product sale logs (from completed orders)
        $data['sales'] = UseProduct::where('from_user_id', $userId)
            ->with(['product', 'saleLog']) // Eager load product and saleLog relationships
            ->orderBy('created_at', 'desc') // Order by created_at for better UX
            ->get();
            

        return view('prime_user.product_sale.index', $data);
    }

    public function used_product()
    {
        $userId = auth()->id();

        // Get all used products for the authenticated user
        $data['usedProducts'] = UseProduct::where('user_id', $userId)
            ->with(['product', 'fromUser']) // Eager load product and fromUser relationships
            ->orderBy('created_at', 'desc') // Order by created_at for better UX
            ->get();

        return view('prime_user.used_product.index', $data);
    }

    public function sold_product(){

        $userId = auth()->id();

        // Get all used products for the authenticated user
        $data['soldProducts'] = UseProduct::where('from_user_id', $userId)
            ->with(['product', 'fromUser']) // Eager load product and fromUser relationships
            ->orderBy('created_at', 'desc') // Order by created_at for better UX
            ->get();

        return view('prime_user.sold_product.index', $data);
    }
}
