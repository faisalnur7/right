<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SaleLog;
use App\Models\BinaryTreeNode;
use App\Models\PrimeTransaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(){
        $data['title'] = $data['page_title'] = "Dashboard";
        return view('home', $data);
    }

    public function dashboard()
    {
        $user = auth()->user()->load(['primeWallet', 'affiliateWallet', 'activePackage']);

        $saleLogs = SaleLog::all()->map(function ($saleLog) use ($user) {
            $saleLog->count = $user->activationCountBySaleLog($saleLog->id);
            return $saleLog;
        });

        $packageUser = $user->activePackage->first();
        $daysRemaining = null;

        if ($packageUser && $packageUser->pivot->expires_at) {
            $daysRemaining = Carbon::now()->startOfDay()
                ->diffInDays(Carbon::parse($packageUser->pivot->expires_at)->startOfDay(), false);
        }

        $todayTransactions = PrimeTransaction::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->get();

        $allTransactions = PrimeTransaction::where('user_id', $user->id)->get();

        $todayIncome = $todayTransactions->where('amount_in', '>', 0)->sum('amount_in');

        $todaySubscriptionIncome = $todayTransactions
            ->whereIn('type', [
                PrimeTransaction::DIRECT_SUBSCRIPTION_COMMISSION,
                PrimeTransaction::INDIRECT_SUBSCRIPTION_COMMISSION,
            ])
            ->sum('amount_in');

        $todayAffiliateIncome = $todayTransactions
            ->whereIn('type', [
                PrimeTransaction::DIRECT_PRODUCT_PURCHASE_COMMISSION,
                PrimeTransaction::INDIRECT_PRODUCT_PURCHASE_COMMISSION
            ])
            ->sum('amount_in');
        $todayAssociateCommission = $todayTransactions->where('type',PrimeTransaction::ASSOCIATE_COMMISSION)->sum('amount_in');

        $todayLeadsIncome = $todayTransactions
            ->whereIn('type', [
                PrimeTransaction::DIRECT_PRODUCT_USE_COMMISSION,
                PrimeTransaction::INDIRECT_PRODUCT_USE_COMMISSION,
            ])
            ->sum('amount_in');

        $totalIncome = $allTransactions->where('amount_in', '>', 0)->sum('amount_in');
        $totalDisbursement = $allTransactions
            ->where('type', PrimeTransaction::DISBURSEMENT)
            ->sum('amount_out');

        $data = [
            'primeBalance'           => $user->primeWallet->balance ?? 0,
            'affiliateBalance'       => $user->affiliateWallet->balance ?? 0,
            'saleLogs'               => $saleLogs,
            'daysRemaining'          => $daysRemaining,
            'todayIncome'            => $todayIncome,
            'todaySubscriptionIncome'=> $todaySubscriptionIncome,
            'todayAffiliateIncome'   => $todayAffiliateIncome,
            'todayAssociateCommission' => $todayAssociateCommission,
            'todayLeadsIncome'       => $todayLeadsIncome,
            'totalIncome'            => $totalIncome,
            'totalDisbursement'      => $totalDisbursement,
        ];

        return view('dashboard', $data);
    }
}
