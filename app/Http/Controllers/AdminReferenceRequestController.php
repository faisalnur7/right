<?php

namespace App\Http\Controllers;

use App\Models\AdminReferenceRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AdminReferenceRequestController extends Controller
{

    public function index(){
        $data['adminReferenceRequest'] = AdminReferenceRequest::query()->where('status','1')->get();
        $data['primeUsers'] = User::query()->where('user_affiliate_type', User::PRIME)->get();
        return view('admin.admin_reference_requests.list', $data);
    }

    public function approve(Request $request, $id)
    {
        $requestData = AdminReferenceRequest::findOrFail($id);

        $request->validate([
            'prime_user_id' => 'required|exists:users,id',
        ]);

        // update new reference
        $requestData->update([
            'new_reference_user_id' => $request->prime_user_id,
            'status' => '2', // approved
        ]);

        $user = $requestData->user;
        $user->reference_user_id = $request->prime_user_id;
        $user->prime_verified = User::PRIME_VERIFIED_STATUS_VERIFIED;
        $user->save();

        return redirect()->route('admin_reference_request.list')
                        ->with('success', 'Reference request approved successfully.');
    }
}
