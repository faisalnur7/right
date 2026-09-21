<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\UserSaleLogUnit;
use App\Models\BinaryTreeNode;
use Illuminate\Support\Facades\DB;

class UpdateUserSaleLogWithBinaryTree extends Command
{
    protected $signature = 'update:user-sale-log';
    protected $description = 'Update UserSaleLogUnit and BinaryTreeNode status in batches';

    public function handle()
    {
        $this->info('Starting UserSaleLog update process...');
        
        $batchSize = 5000;
        $totalUpdated = 0;

        $binaryNode = BinaryTreeNode::where('status', 0)
                ->where('active_in_sale_log', 1)
                ->first();

        if (empty($binaryNode)) {
            $this->info('No active Nodes found to process.');
            return;
        }
        

        do {
            $updatedCount = BinaryTreeNode::where('status', 0)
                ->where('active_in_sale_log', 1)
                ->limit($batchSize)
                ->update(['active_in_sale_log' => 0]);

            $totalUpdated += $updatedCount;

            if ($updatedCount > 0) {
                $this->info("Batch updated {$updatedCount} rows. Total so far: {$totalUpdated}");
            }

        } while ($updatedCount > 0);
        
        return 0;
    }
}
