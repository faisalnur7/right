<?php

namespace App\Http\Controllers;

use App\Models\UserActiveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserActiveRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'sale_log_id' => 'required|exists:sale_logs,id',
        ]);

        UserActiveRequest::create([
            'user_id' => Auth::id(),
            'present_reference_user_id' => $request->present_reference_user_id,
            'new_reference_id' => $request->new_reference_id,
            'product_id' => $request->product_id,
            'sale_log_id' => $request->sale_log_id,
            'status' => 1,
        ]);

        return back()->with('success', 'Request sent to admin successfully.');
    }

    /**
     * Update status (admin only).
     */
    public function update(Request $request, UserActiveRequest $productSaleLog)
    {
        $request->validate([
            'status' => 'required|integer',
        ]);

        $productSaleLog->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Request status updated.');
    }

    /**
     * Delete a request.
     */
    public function destroy(ProductSaleLog $productSaleLog)
    {
        $productSaleLog->delete();
        return back()->with('success', 'Request deleted.');
    }
}
