<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\UseProduct;
use App\Models\SaleLog;
use App\Models\BinaryTreeNode;
use App\Models\UserActiveRequest;
use Illuminate\Http\Request;

class PrimeStockController extends Controller
{
    public function prime_stock(Request $request)
    {
        $userId = auth()->id();
        $saleLogId = $request->sale_log_id;
        $data['saleLogs'] = SaleLog::all();

        // Build base stock logs query
        $stockLogsQuery = OrderItem::whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', Order::COMPLETED);
            })
            ->selectRaw('product_id, sale_log_id, SUM(quantity) as total_quantity')
            ->with('product', 'sale_log')
            ->groupBy('product_id', 'sale_log_id');

        // Add condition only if sale_log_id is provided
        if (!empty($saleLogId)) {
            $stockLogsQuery->where('sale_log_id', $saleLogId);
        }

        $data['stockLogs'] = $stockLogs = $stockLogsQuery->get();
        $data['stockLogsQuery'] = $stockLogsQuery;
        
        $isReferenceActiveInSaleLog = BinaryTreeNode::query()->where('user_id', auth()->user()?->referenceUser?->id)->exists();
        $data['referenceUser'] = auth()->user()?->referenceUser;
        $activeReferenceSaleLogIds = [];

        foreach($data['saleLogs'] as $saleLog){
            $isReferenceActiveInSaleLog = BinaryTreeNode::query()->where('user_id',auth()->user()?->referenceUser?->id)->exists();
            if($isReferenceActiveInSaleLog){
                array_push($activeReferenceSaleLogIds, $saleLog->id);
            }
        }

        $data['activeReferenceSaleLogIds'] = $activeReferenceSaleLogIds;

        foreach ($stockLogs as $log) {
            $usedQuantity = UseProduct::where(function ($query) use ($userId) {
                    $query->where('from_user_id', $userId)
                        ->orWhere('user_id', $userId);
                })
                ->where('product_id', $log->product_id)
                ->where('sale_log_id', $log->sale_log_id)
                ->sum('quantity');

            $log->total_quantity = max(0, $log->total_quantity - $usedQuantity);
        }

        $data['users'] = User::where('id', '!=', $userId)->where('is_super_prime',0)->get();

        $data['existingRequests'] = UserActiveRequest::where('user_id', $userId)->pluck('sale_log_id', 'product_id');
        
        return view('prime_user.stock.index', $data);
    }


    public function prime_sale_log(){
        $userId = auth()->id();

        // Get all product sale logs (from completed orders)
        $saleLogs = UseProduct::where('from_user_id', $userId)
            ->with(['product', 'saleLog']) // Eager load product and saleLog relationships
            ->orderBy('created_at', 'desc') // Order by created_at for better UX
            ->get();

        return view('prime_user.product_sale.index', [
            'saleLogs' => $saleLogs,
        ]);
    }

}
