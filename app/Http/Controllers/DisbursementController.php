<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Disbursement;
use App\Models\PrimeTransaction;
use App\Models\GeneralSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class DisbursementController extends Controller
{
    public function disbursement_list(Request $request)
    {
        $per_page = $request->per_page ?: 10;

        // Business day start (assuming only one row in GeneralSettings)
        $businessStart = GeneralSetting::first()->business_start_date;
        $start = Carbon::parse($businessStart)->startOfDay();
        $today = Carbon::today();

        // Generate business days array for dropdown
        $businessDays = [];
        $diff = $start->diffInDays($today) + 1; // include today
        for ($i = 1; $i <= $diff; $i++) {
            $date = $start->copy()->addDays($i - 1);
            $businessDays[$i] = $date->format('d M Y'); // Day N (dd Mmm YYYY)
        }

        $data['businessDays'] = $businessDays;

        $data['date'] = $date = $request->business_day ? Carbon::createFromFormat('d M Y', $request->business_day) : $today;

        $targetDate = $request->business_day 
            ? Carbon::parse($request->business_day) 
            : Carbon::now(); // the date you want the business day number

        $businessDayNumber = $businessStart 
            ? Carbon::parse($businessStart)->diffInDays($targetDate) + 1 // convert to Carbon first
            : null;

        $data['business_day_number'] = $businessDayNumber;

        $data['users'] = User::query()
                            ->leftJoin('binary_tree_nodes as btn', 'btn.user_id', '=', 'users.id')
                            ->where('users.prime_verified', User::PRIME_VERIFIED_STATUS_COMPLETED)
                            ->where('users.is_active', User::USER_PACKAGE_ACTIVE)
                            ->whereExists(function($query) use ($date) {
                                $query->select(DB::raw(1))
                                    ->from('prime_transactions as pt')
                                    ->whereColumn('pt.user_id', 'users.id')
                                    ->whereDate('pt.created_at', $date)
                                    ->where('amount_in', '>', 0);
                            })
                            ->select(
                                'users.id',
                                'users.name',
                                'users.email',
                                'users.phone',
                                'users.reference_user_id',
                                'users.is_super_prime',
                                DB::raw('COALESCE(SUM(btn.activation_number), 0) as total_activation')
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
                            ->paginate($per_page)
                            ->appends($request->all());


        $data['users']->getCollection()->transform(function($user) use ($date) {
            $user->total_amount_in = $user->transactions()
                ->whereDate('created_at', $date)
                ->whereIn('type',
                                [
                                    PrimeTransaction::DIRECT_PRODUCT_USE_COMMISSION,
                                    PrimeTransaction::INDIRECT_PRODUCT_USE_COMMISSION,
                                    PrimeTransaction::DIRECT_PRODUCT_PURCHASE_COMMISSION,
                                    PrimeTransaction::INDIRECT_PRODUCT_PURCHASE_COMMISSION,
                                    PrimeTransaction::DIRECT_SUBSCRIPTION_COMMISSION,
                                    PrimeTransaction::INDIRECT_SUBSCRIPTION_COMMISSION,
                                    PrimeTransaction::ASSOCIATE_COMMISSION,
                                ]
                )
                ->sum('amount_in');

            $user->is_paid = 0;
            if($user->total_amount_in > 0){
                $user->is_paid = Disbursement::query()->where('user_id',$user->id)->whereDate('disburse_date', $date)->first() ? 1 : 0;
                $user->total_paid = Disbursement::query()->where('user_id',$user->id)->whereDate('disburse_date', $date)->sum('total_amount');

                if(($user->total_amount_in - $user->total_paid) > 0){
                    $user->is_paid = 2;
                }
            }

            return $user;
        });


        return view('admin.disbursement.index', $data);
    }

    public function disbursement_list_completed(Request $request){
        $per_page = $request->per_page ?: 10;
        $saleLogId = $request->sale_log_id ?: null;

        return view('admin.disbursement.index', compact('users'));
    }

    public function bulkDisburse(Request $request)
    {
        $userIds = $request->input('user_ids', []);
        $businessDay = $request->selected_business_day 
            ? Carbon::parse($request->selected_business_day) 
            : Carbon::today();
        $businessDayNumber = $request->business_day_number;

        DB::transaction(function() use ($userIds, $businessDay, $businessDayNumber) {
            $notificationService = new NotificationService();
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if (!$user) continue;

                $income = $user->getIncomeByDate($businessDay);
                $disbursed_amount = $user->getDisbursementByDate($businessDay);
                // Only disburse if total > 0
                if ((float)$disbursed_amount['total'] > 0) {
                    // Create PrimeTransaction
                    $primeTransactionData = [
                        'user_id' => $user->id,
                        'source_user_id' => null,
                        'type' => PrimeTransaction::DISBURSEMENT,
                        'amount_in' => null,
                        'amount_out' => $disbursed_amount['total'],
                        'description' => null,
                    ];

                    $primeTransaction = PrimeTransaction::create($primeTransactionData);

                    // Create Disbursement
                    $data = [
                        'user_id'             => $userId,
                        'prime_transaction_id'=> $primeTransaction->id,
                        'business_day'        => $businessDayNumber,
                        'disburse_date'       => $businessDay,
                        'account_type'        => $user->kyc->account_type,
                        'account_number'      => $user->kyc->account_number,
                        'leads_amount'        => $disbursed_amount['leads'],
                        'affiliate_amount'    => $disbursed_amount['affiliate'],
                        'subscription_amount' => $disbursed_amount['subscription'],
                        'associate_amount'    => $disbursed_amount['associate'],
                        'total_amount'        => $disbursed_amount['total'],
                    ];

                    Disbursement::create($data);

                    $wallet = $user->primeWallet;
                    if ($wallet && $wallet->balance >= $disbursed_amount['total']) {
                        $wallet->balance -= $disbursed_amount['total'];
                        $wallet->save();

                        $notificationData = [
                            'type' => 'Disbursement',
                            'amount' => $disbursed_amount['total'],
                            'transaction' => $primeTransaction->id
                        ];
                        $notificationService->create('wallet_disbursement_notification', $user->id, $notificationData, 0);
                    }
                }
            }
        });

        return response()->json(['message' => 'Disbursement successful for selected users.']);
    }



}
