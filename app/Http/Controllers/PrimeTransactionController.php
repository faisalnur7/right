<?php

namespace App\Http\Controllers;

use App\Models\PrimeTransaction;
use App\Models\User;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PrimeTransactionController extends Controller
{
    public function prime_transactions(){
        $perPage = request()->get('per_page', 15); // default 15

        $businessStartDate = GeneralSetting::first();
        $startDate = Carbon::parse($businessStartDate->business_start_date);

        $data['primeTransaction'] = PrimeTransaction::with('sourceUser')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate($perPage)
            ->through(function ($transaction) use ($startDate) {
                $transaction->business_day = (integer)$startDate->diffInDays($transaction->created_at); // +1 if you want day count starting from 1
                return $transaction;
            });

        return view('prime_user.prime_transaction.transaction_list', $data);
    }
}
        // $users = User::all();

        // foreach($users as $user){
        //     $userWallet = $user->primeWallet;

        //     if(!empty($userWallet->balance)){
        //         $userWallet->balance = $user->transactions->sum('amount_in') - $user->transactions->sum('amount_out');
        //         $userWallet->save();
        //     }
        // }