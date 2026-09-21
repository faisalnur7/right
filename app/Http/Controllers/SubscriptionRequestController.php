<?php

namespace App\Http\Controllers;

use App\Models\PrimeRequest;
use App\Models\PackageUser;
use App\Models\User;
use App\Models\Wallet;
use App\Models\SubscriptionPackage;
use App\Models\PrimeTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;
use Throwable;

class SubscriptionRequestController extends Controller
{
    public function pending_list(){
        $data['packageUsers'] = PackageUser::with('payment_options','subscription_package','user')->where('status', User::USER_PACKAGE_PENDING)->get();
        return view('admin.subscription_requests.subscriptio_request', $data);
    }
    


    public function respond(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $packageUser = PackageUser::findOrFail($id);
                $packageUser->update(['status' => $request->status]);

                $user = User::findOrFail($packageUser->user_id);

                if ($packageUser->status == User::USER_PACKAGE_ACTIVE) {
                    // Set subscription start and expiry dates
                    $packageUser->assigned_at = now();
                    $packageUser->expires_at = now()->addDays($packageUser->subscription_package->duration);
                    $packageUser->save();

                    // Set user as Prime
                    $user->prime_verified = User::PRIME_VERIFIED_STATUS_COMPLETED;
                    $user->user_affiliate_type = User::PRIME;
                    $user->is_active = 1;

                    // Handle Prime referral info
                    $primeRequest = PrimeRequest::where('requester_id', $user->id)->first();
                    if ($primeRequest && $primeRequest->prime) {
                        $primeUser = $primeRequest->prime;

                        $refered_user_count = User::where('reference_user_id', $primeUser->id)
                            ->where('id', '!=', $user->id)
                            ->count();

                        $reference_id = 'RB-' . sprintf('%04.3d', $primeUser->id) . '-' . sprintf('%04.3d', $refered_user_count + 1);

                        $user->reference_user_id = $primeUser->id;
                        $user->reference_id = $reference_id;

                        // Update KYC affiliate ID
                        if ($user->kyc) {
                            $user->kyc->affiliate_id = $reference_id;
                            $user->kyc->save();
                        }
                    }

                    $user->save();

                    // Create or update Prime Wallet
                    Wallet::firstOrCreate(
                        ['user_id' => $user->id, 'type' => 'prime'],
                        ['balance' => 0]
                    );

                    // Create or update Affiliate Wallet
                    Wallet::firstOrCreate(
                        ['user_id' => $user->id, 'type' => 'affiliate'],
                        ['balance' => 0]
                    );

                    // Distribute commissions
                    $this->distribute_subscription_commission($user, $packageUser->subscription_package);
                }

                // Handle inactive case
                if ($packageUser->status == User::USER_PACKAGE_INACTIVE) {
                    $user->prime_verified = User::PRIME_VERIFIED_STATUS_PACKAGE;
                    $user->save();
                }

                // prime_user_payment_approved
                $notificationData = ['name' => $user->name, 'affiliate_id' => $user->kyc->affiliate_id];
                $notificationService = new NotificationService();    
                $notificationService->create('prime_user_payment_approved', $user->id, $notificationData, 0);
            });

            return back()->with('success', 'Response recorded.');
        } catch (Throwable $e) {
            Log::error('PackageUser respond error: ' . $e->getMessage(), [
                'user_id' => $id,
                'exception' => $e,
            ]);

            return back()->with('error', 'An error occurred while processing the request.');
        }
    }


    public function distribute_subscription_commission(User $user, SubscriptionPackage $package)
    {
        $referenceUser = $user->referenceUser;
        $referenceUserOfReferrer = $user->reference_user_of_referrer;

        if (!$referenceUser && !$referenceUserOfReferrer) {
            return;
        }

        $notificationData = ['name' => $user->name, 'affiliate_id' => $user->kyc->affiliate_id];
        $notificationService = new NotificationService();    

        $tier1Total = 0;
        $tier2Total = 0;

        $totalCommission = $package->total_commission ?? 0;
        $tier1Total = $this->calculateCommission($package->tier1_percentage, $totalCommission);
        $tier2Total = $this->calculateCommission($package->tier2_percentage, $totalCommission);

        $this->creditUserWalletAndLogTransaction(
            $referenceUser,
            $tier1Total,
            $user->id,
            PrimeTransaction::DIRECT_SUBSCRIPTION_COMMISSION
        );

        $this->creditUserWalletAndLogTransaction(
            $referenceUserOfReferrer,
            $tier2Total,
            $user->id,
            PrimeTransaction::INDIRECT_SUBSCRIPTION_COMMISSION
        );

        $notificationData1 = [
            'type' => 'Subscription Bonus', 
            'amount' => $tier1Total, 
        ];

        $notificationData2 = [
            'type' => 'Subscription Bonus', 
            'amount' => $tier2Total, 
        ];

        $notificationService->create('wallet_notification', $referenceUser->id, $notificationData1, 0);
        $notificationService->create('wallet_notification', $referenceUserOfReferrer->id, $notificationData2, 0);
    }

    public function creditUserWalletAndLogTransaction($refUser, $amount, $sourceUserId, $type = null)
    {
        if (!$refUser || $amount <= 0) {
            return;
        }

        $wallet = $refUser->primeWallet;

        if ($wallet) {
            $wallet->balance += $amount;
            $wallet->save();

            PrimeTransaction::create([
                'user_id'        => $refUser->id,
                'type'           => $type,
                'source_user_id' => $sourceUserId,
                'amount_in'      => $amount,
                'amount_out'     => 0,
            ]);
        }
    }

    protected function calculateCommission(float $percentage, float $total): float
    {
        return round(($percentage * $total) / 100, 2);
    }

}
