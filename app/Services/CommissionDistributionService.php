<?php

namespace App\Services;

use App\Models\User;
use App\Models\PrimeTransaction;
use App\Models\BinaryTreeNode;
use App\Services\NotificationService;

class CommissionDistributionService
{
    /**
     * Distribute all applicable commissions for a product usage.
     *
     * @param  \App\Models\BinaryTreeNode  $node
     * @param  \App\Models\User  $sourceUser
     * @param  int  $saleLogId
     * @param  float|null  $uplineCommission
     * @param  float|null  $associateCommission
     * @param  int  $depth
     * @return void
     */
    public function distributeAllCommissions(
        ?BinaryTreeNode $node,
        User $sourceUser,
        int $saleLogId,
        ?float $uplineCommission = 0,
        ?float $associateCommission = 0,
        int $depth = 3
    ): void {
        $notificationService = new NotificationService();
        // 💠 1. Distribute to upline ancestors
        if (!empty($node) && $uplineCommission > 0) {
            $ancestors = BinaryTreeNode::getAncestorChain($node, $saleLogId, $depth);

            foreach ($ancestors as $level => $parent) {
                $wallet = $parent->user->primeWallet;

                if ($wallet) {
                    $wallet->increment('balance', $uplineCommission);
                }

                PrimeTransaction::create([
                    'user_id'        => $parent->user_id,
                    'source_user_id' => $sourceUser->id,
                    'sale_log_id'    => $saleLogId,
                    'type'           => $level === 1
                        ? PrimeTransaction::DIRECT_PRODUCT_USE_COMMISSION
                        : PrimeTransaction::INDIRECT_PRODUCT_USE_COMMISSION,
                    'amount_in'      => $uplineCommission,
                    'amount_out'     => 0,
                    'level'          => $level,
                ]);
                
                $notificationData = [
                    'type' => 'Leads commission',
                    'amount' => $uplineCommission,
                ];
                $notificationService->create('wallet_notification', $parent->user_id, $notificationData, 0);
            }
        }

        // 💠 2. Distribute Associate Commission to Super Prime user
        if ($associateCommission > 0) {
            $superPrimeUser = User::where('is_super_prime', 1)->first();

            if ($superPrimeUser && $superPrimeUser->primeWallet) {
                $superPrimeUser->primeWallet->increment('balance', $associateCommission);

                PrimeTransaction::create([
                    'user_id'        => $superPrimeUser->id,
                    'source_user_id' => $sourceUser->id,
                    'sale_log_id'    => $saleLogId,
                    'type'           => PrimeTransaction::ASSOCIATE_COMMISSION,
                    'amount_in'      => $associateCommission,
                    'amount_out'     => 0,
                ]);

                $notificationData = [
                    'type' => 'Associate commission',
                    'amount' => $associateCommission,
                ];
                $notificationService->create('wallet_notification', $parent->user_id, $notificationData, 0);
                
            }
        }
    }
}
