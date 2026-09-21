<?php

namespace App\Http\Controllers;

use App\Models\PrimeRequest;
use App\Models\User;
use App\Models\Kyc;
use App\Models\SaleLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrimeRequestController extends Controller
{
    public function approved_prime_requests(Request $request)
    {
        $perPage   = $request->per_page ?: 10;
        $saleLogId = $request->sale_log_id ?: null;
        $authUser  = auth()->user();

        $baseQuery = User::query()
            ->where('users.prime_verified', User::PRIME_VERIFIED_STATUS_COMPLETED)
            ->where('users.is_active', User::USER_PACKAGE_ACTIVE)
            ->where('users.reference_user_id', $authUser->id);

        $users_query = (clone $baseQuery)
            ->leftJoin('binary_tree_nodes as btn', 'btn.user_id', '=', 'users.id')
            ->when($saleLogId, fn($q) => $q->where('btn.sale_log_id', $saleLogId))
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'users.reference_user_id',
                'users.is_super_prime',
                DB::raw('COALESCE(SUM(btn.activation_number),0) as total_activation')
            )
            ->groupBy(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'users.reference_user_id',
                'users.is_super_prime'
            )
            ->orderByDesc('users.is_super_prime')
            ->orderByDesc('total_activation')
            ->paginate($perPage);

        $data = [
            'totalUsers'   => (clone $baseQuery)->count(),
            'activeUsers'  => 0,
            'inactiveUsers'=> 0,
            'users'        => $users_query,
            'saleLogs'     => SaleLog::all(),
            'per_page'     => $perPage,
        ];

        if ($saleLogId) {
            // Reusable closure for status-based counts
            $statusCount = function($status) use ($baseQuery, $saleLogId) {
                return (clone $baseQuery)
                    ->join('binary_tree_nodes as btn', 'btn.user_id', '=', 'users.id')
                    ->where('btn.status', $status)
                    ->where('btn.sale_log_id', $saleLogId)
                    ->distinct('users.id')
                    ->count('users.id');
            };

            $data['activeUsers']   = $statusCount(1);
            $data['inactiveUsers'] = $statusCount(0);
        }

        return view('prime_user.prime_request.list', $data);
    }

    public function rejected_prime_requests(){
        $data['primeRequests'] = PrimeRequest::query()->where('prime_id', auth()->user()->id)->where('status', PrimeRequest::REJECTED)->paginate(10);
        return view('prime_user.prime_request.index', $data);
    }

    public function general_affiliates(){
        $data['users'] = User::query()->where('reference_user_id', auth()->user()->id)->where('user_affiliate_type',1)->paginate(10);
        return view('prime_user.prime_request.affiliate_list', $data);
    }

    public function prime_requests(){
        $data['primeRequests'] = PrimeRequest::query()->where('prime_id', auth()->user()->id)->where('status', PrimeRequest::PENDING)->paginate(10);
        return view('prime_user.prime_request.index', $data);
    }
    public function store(Request $request)
    {
        $request->validate([
            'prime_id' => 'required|exists:users,id',
        ]);
    
        $existing = PrimeRequest::where([
            ['requester_id', auth()->id()],
            ['prime_id', $request->prime_id]
        ])->first();
    
        if ($existing) {
            return ['status' => 'You have already requested this user.'];
        }

        $primeRequest = PrimeRequest::create([
            'requester_id' => auth()->id(),
            'prime_id' => $request->prime_id,
            'status' => 1,
        ]);

        $notificationData = ['username' => auth()->user()->name, 'phone' => auth()->user()->phone];
        $notificationService = new NotificationService();    
        $notificationService->create('prime_affiliate_request', $request->prime_id, $notificationData, 0);

        return response()->json(['success' => true, 'message' => 'Request sent successfully!']);
    }

    public function cancel_request(Request $request){
        $id = $request->request_id;
        $primeRequest = PrimeRequest::query()->findOrFail($id);
        if(!empty($primeRequest)){
            $primeRequest->delete();

            return ['status' => 'Request cancelled successfully.'];
        }
    }


    public function respond(Request $request, $id)
    {
        $primeRequest = PrimeRequest::query()->findOrFail($id);
        if ($primeRequest->prime_id !== auth()->user()->id) {
            abort(403);
        }

        $primeRequest->update(['status' => $request->status]);
        $refered_user_count = User::query()->where('reference_user_id', auth()->user()->id)->where('id','!=',$primeRequest->requester_id)->count();
        $reference_id = 'RB-'.sprintf('%04.3d', auth()->user()->id).'-'.sprintf('%04.3d', $refered_user_count+1);

        $requester = Kyc::query()->where('user_id',$primeRequest->requester_id)->first();

        $notificationData = ['username' => $primeRequest->prime->name, 'phone' => $primeRequest->prime->phone];
        $notificationService = new NotificationService();    

        if($primeRequest->status == PrimeRequest::REJECTED){
            $notificationService->create('prime_affiliate_request_cancelled', $primeRequest->requester_id, $notificationData, 0);
        }

        if($primeRequest->status == PrimeRequest::ACCEPTED){
            $requester_kyc = Kyc::query()->where('user_id',$primeRequest->requester_id)->first();
            $requester_kyc->affiliate_id = $reference_id;
            $requester_kyc->save();

            $user = User::query()->findOrFail($primeRequest->requester_id);
            $user->prime_verified = User::PRIME_VERIFIED_STATUS_VERIFIED;
            $user->save();

            $notificationData = ['username' => $primeRequest->prime->name, 'phone' => $primeRequest->prime->phone];
            $notificationService = new NotificationService();    
            $notificationService->create('prime_affiliate_request_approved', $primeRequest->requester_id, $notificationData, 0);
        }

        return back()->with('success', 'Response recorded.');
    }

    
}
