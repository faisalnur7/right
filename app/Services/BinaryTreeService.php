<?php

namespace App\Services;

use App\Models\User;
use App\Models\BinaryTreeNode;
use App\Models\UserSaleLogUnit;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class BinaryTreeService
{
    /**
     * Insert a user into the binary tree.
     */
    public function insertIntoBinaryTree(User $user, $saleLogId, $referrer = null)
    {
        return DB::transaction(function () use ($user, $saleLogId, $referrer) {

            if (empty($referrer)) {
                $referrer = User::find($user->reference_user_id);
            }

            $entryCount = BinaryTreeNode::where('user_id', $user->id)
                ->where('sale_log_id', $saleLogId)
                ->count();

            // ✅ Helper: Check if user is active in tree
            $isUserActiveInTree = function ($user, $saleLogId) {
                $logUnit = UserSaleLogUnit::where('user_id', $user->id)
                    ->where('sale_log_id', $saleLogId)
                    ->where('is_active', 1)
                    ->first();

                if (!$logUnit) {
                    return false;
                }

                return BinaryTreeNode::where('user_id', $user->id)
                    ->where('sale_log_id', $saleLogId)
                    ->exists();
            };

            // ✅ Helper: BFS search for left-most slot within subtree
            $findAvailablePositionInSubtree = function ($rootUserId, $saleLogId) {
                $nodes = BinaryTreeNode::where('sale_log_id', $saleLogId)
                    ->orderBy('position', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->get(['id', 'user_id', 'parent_node_id']);

                $childrenMap = [];
                foreach ($nodes as $node) {
                    if ($node->parent_node_id !== null) {
                        $childrenMap[$node->parent_node_id][] = $node->id;
                    }
                }

                $latestRootNode = BinaryTreeNode::where('user_id', $rootUserId)
                    ->where('sale_log_id', $saleLogId)
                    ->latest()
                    ->first();

                if (!$latestRootNode) {
                    return null;
                }

                $queue = collect([$latestRootNode->id]);
                $visited = [];

                while ($queue->isNotEmpty()) {
                    $currentNodeId = $queue->shift();

                    if (isset($visited[$currentNodeId])) {
                        continue;
                    }
                    $visited[$currentNodeId] = true;

                    $node = BinaryTreeNode::find($currentNodeId);
                    if ($node) {
                        $childCount = BinaryTreeNode::where('parent_node_id', $node->id)
                            ->where('sale_log_id', $saleLogId)
                            ->count();

                        if ($childCount < 2) {
                            return $node;
                        }
                    }

                    if (!empty($childrenMap[$currentNodeId])) {
                        foreach ($childrenMap[$currentNodeId] as $childNodeId) {
                            if (!isset($visited[$childNodeId])) {
                                $queue->push($childNodeId);
                            }
                        }
                    }
                }

                return null;
            };

            // ✅ Helper: Get next position (1=left, 2=right)
            $getNextPosition = function ($parentNodeId, $saleLogId) {
                if (!$parentNodeId) {
                    return 1;
                }

                $existingChildren = BinaryTreeNode::where('parent_node_id', $parentNodeId)
                    ->where('sale_log_id', $saleLogId)
                    ->orderBy('position', 'asc')
                    ->get();

                if ($existingChildren->isEmpty()) {
                    return 1;
                }

                if ($existingChildren->count() == 1) {
                    return 2;
                }

                throw new \Exception('Parent already has 2 children');
            };

            // ✅ Helper: Deactivate parent if descendant count hits limit
            $checkAndDeactivateParent = function (BinaryTreeNode $greatParent) {
                $descendantCount = BinaryTreeNode::where('great_parent_id', $greatParent->id)->count();

                if ($descendantCount == 8 && !$greatParent->is_super_prime) {
                    if ($greatParent->status == BinaryTreeNode::ACTIVE_IN_TREE) {
                        $greatParent->status = BinaryTreeNode::INACTIVE_IN_TREE;
                        $greatParent->save();

                        $notificationService = new NotificationService();
                        $notificationData = [
                            'name' => $greatParent->user->name,
                            'saleLog' => $greatParent->saleLog->name,
                        ];
                        $notificationService->create('sale_log_completed', $greatParent->user_id, $notificationData, 0);
                    }
                }
            };

            // ✅ Main logic: choose parent
            $parent = null;

            if ($referrer && $isUserActiveInTree($referrer, $saleLogId)) {
                $latestReferrerNode = BinaryTreeNode::where('user_id', $referrer->id)
                    ->where('sale_log_id', $saleLogId)
                    ->latest()
                    ->first();

                if ($latestReferrerNode) {
                    $childCount = BinaryTreeNode::where('parent_node_id', $latestReferrerNode->id)
                        ->where('sale_log_id', $saleLogId)
                        ->count();

                    if ($childCount < 2) {
                        $parent = $latestReferrerNode;
                    } else {
                        $parent = $findAvailablePositionInSubtree($referrer->id, $saleLogId);
                    }
                }
            }

            $parentLevel = $parent ? $parent->level : -1;
            $position = $getNextPosition($parent?->id, $saleLogId);

            // ✅ Create new node
            $treeData = [
                'user_id'           => $user->id,
                'parent_id'         => $parent?->user_id,
                'parent_node_id'    => $parent?->id,
                'level'             => $parentLevel + 1,
                'position'          => $position,
                'activation_number' => $entryCount + 1,
                'sale_log_id'       => $saleLogId,
                'created_at'        => now(),
            ];

            $latestNode = BinaryTreeNode::create($treeData);

            // ✅ Calculate and save great_parent_id
            $greatParent = $latestNode->getGreatParentNodeId();
            $latestNode->great_parent_id = $greatParent?->id;
            $latestNode->save();

            // ✅ Check and deactivate if needed
            if (!empty($greatParent)) {
                $checkAndDeactivateParent($greatParent);
            }

            return $latestNode;
        });
    }
}
