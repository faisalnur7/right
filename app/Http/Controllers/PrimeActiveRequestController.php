<?php

namespace App\Http\Controllers;

use App\Models\UserActiveRequest;
use App\Models\User;
use App\Models\Product;
use App\Models\BinaryTreeNode;
use App\Models\UserSaleLogUnit;
use App\Models\UseProduct;
use App\Models\PrimeActiveRequest;
use App\Models\PrimeTransaction;
use Illuminate\Http\Request;
use App\Services\BinaryTreeService;
use App\Services\CommissionDistributionService;

class PrimeActiveRequestController extends Controller
{
    public function index(){
        $data['adminReferenceRequest'] = UserActiveRequest::query()->get();
        $data['primeUsers'] = BinaryTreeNode::query()->where('status',1)->get();
        return view('admin.prime_active_requests.list', $data);
    }

    public function approve(Request $request, $id)
    {
        // ✅ Step 1: Validate input and fetch the request
        $validated = $request->validate([
            'new_reference_id' => 'required|exists:users,id',
        ]);

        $requestData = UserActiveRequest::findOrFail($id);

        // ✅ Step 2: Update the request status and new reference
        $requestData->update([
            'new_reference_id' => $validated['new_reference_id'],
            'status'           => '2', // approved
        ]);

        // ✅ Step 3: Update user's temporary reference
        $user = $requestData->user;
        $user->temp_reference_user_id = $validated['new_reference_id'];
        $user->save();

        // ✅ Step 4: Verify the product and related sale log
        $product = $requestData->product;
        if (!$product) {
            return back()->with('error', 'Product not found.');
        }

        $saleLog = $product->saleLogs->firstWhere('id', $requestData->sale_log_id);
        if (!$saleLog || !$saleLog->pivot) {
            return back()->with('error', 'Invalid sale log or pivot data missing.');
        }

        // ✅ Step 5: Record product usage
        UseProduct::create([
            'user_id'      => $user->id,
            'product_id'   => $product->id,
            'sale_log_id'  => $saleLog->id,
            'quantity'     => 1,
            'price'        => $product->price,
            'total'        => $product->price,
            'status'       => UseProduct::USED_PRODUCT,
            'used_at'      => now(),
            'remarks'      => $request->remarks ?? null,
            'validity'     => $saleLog->pivot->unit,
            'unit_per_day' => $saleLog->pivot->price,
        ]);

        // ✅ Step 6: Manage User Sale Log Units
        $unitCount = $saleLog->pivot->unit;

        $logUnit = UserSaleLogUnit::firstOrCreate([
            'user_id'     => $user->id,
            'sale_log_id' => $saleLog->id,
        ]);

        $logUnit->increment('remaining_units', $unitCount);
        $logUnit->is_active = $logUnit->remaining_units > 0 ? 1 : 0;
        $logUnit->save();

        // ✅ Step 7: Binary Tree Placement (via Service)
        $binaryTreeService = new BinaryTreeService();
        $referrer = $requestData->newReference; // relationship assumed as `newReference()`
        $node = $binaryTreeService->insertIntoBinaryTree($user, $saleLog->id, $referrer);

        // ✅ Step 8: Distribute Commissions
        if ($node) {
            $commissionService = new CommissionDistributionService();
            $commissionService->distributeAllCommissions(
                $node,
                $user,
                $saleLog->id,
                $product->total_use_commission ?? 0,
                $product->associate_commission ?? 0
            );
        }

        // ✅ Step 9: Redirect success
        return redirect()
            ->route('admin_reference_request.list')
            ->with('success', 'Reference request approved successfully.');
    }
}
