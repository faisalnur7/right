<?php

namespace App\Services;

use App\Models\User;
use App\Models\SaleLog;
use App\Models\GeneralSetting;
use App\Models\BinaryTreeNode;
use App\Models\UserSaleLogUnit;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class BinaryTreeService
{
    /**
     * Single entry point for placing a user after a product is used.
     *
     * - Re-use setting OFF: legacy behaviour, always a (basic) placement.
     * - Re-use setting ON : if the user already has an active basic ID in this
     *   sale log the new node is a clone under it, otherwise a new basic ID.
     */
    public function placeUser(User $user, $saleLogId, $referrer = null)
    {
        if (GeneralSetting::reuseEnabled() && $this->activeBasicNode($user->id, $saleLogId)) {
            return $this->insertClone($user, $saleLogId);
        }

        return $this->insertIntoBinaryTree($user, $saleLogId, $referrer);
    }

    /**
     * The user's current active basic (non clone) node in a sale log.
     */
    public function activeBasicNode($userId, $saleLogId): ?BinaryTreeNode
    {
        return BinaryTreeNode::basic()
            ->where('user_id', $userId)
            ->where('sale_log_id', $saleLogId)
            ->where('status', BinaryTreeNode::ACTIVE_IN_TREE)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Insert a user into the binary tree as a basic ID.
     */
    public function insertIntoBinaryTree(User $user, $saleLogId, $referrer = null)
    {
        return DB::transaction(function () use ($user, $saleLogId, $referrer) {

            $this->lockSaleLogTree($saleLogId);

            if (empty($referrer)) {
                $referrer = User::find($user->reference_user_id);
            }

            // Only basic IDs count as activations.
            $entryCount = BinaryTreeNode::basic()
                ->where('user_id', $user->id)
                ->where('sale_log_id', $saleLogId)
                ->count();

            $parent = null;

            if ($referrer && $this->isUserActiveInTree($referrer, $saleLogId)) {
                // Followers always attach to the referrer's basic ID, never to a clone.
                $latestReferrerNode = BinaryTreeNode::basic()
                    ->where('user_id', $referrer->id)
                    ->where('sale_log_id', $saleLogId)
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->first();

                if ($latestReferrerNode) {
                    $childCount = BinaryTreeNode::where('parent_node_id', $latestReferrerNode->id)
                        ->where('sale_log_id', $saleLogId)
                        ->count();

                    $parent = $childCount < 2
                        ? $latestReferrerNode
                        : $this->findAvailableSlot($latestReferrerNode, $saleLogId);
                }
            }

            $latestNode = $this->createNode($user, $saleLogId, $parent, [
                'activation_number' => $entryCount + 1,
                'is_clone'          => false,
            ]);

            $this->handleCompletion($latestNode);

            return $latestNode;
        });
    }

    /**
     * Insert a clone of the user under the user's active basic ID.
     */
    public function insertClone(User $user, $saleLogId)
    {
        return DB::transaction(function () use ($user, $saleLogId) {

            $this->lockSaleLogTree($saleLogId);

            $basic = $this->activeBasicNode($user->id, $saleLogId);
            if (!$basic) {
                throw new \Exception('No active basic ID found for this user in the sale log.');
            }

            $parent = $this->findAvailableSlot($basic, $saleLogId);
            if (!$parent) {
                throw new \Exception('No available position found under the basic ID.');
            }

            $latestNode = $this->createNode($user, $saleLogId, $parent, [
                'activation_number' => 0, // clones are not activations
                'is_clone'          => true,
            ]);

            $this->handleCompletion($latestNode);

            return $latestNode;
        });
    }

    /**
     * Serialize all placements inside one sale log tree.
     */
    private function lockSaleLogTree($saleLogId): void
    {
        SaleLog::whereKey($saleLogId)->lockForUpdate()->first();
    }

    /**
     * Helper: user has an active unit and a basic node in the sale log.
     */
    private function isUserActiveInTree(User $user, $saleLogId): bool
    {
        $logUnit = UserSaleLogUnit::where('user_id', $user->id)
            ->where('sale_log_id', $saleLogId)
            ->where('is_active', 1)
            ->first();

        if (!$logUnit) {
            return false;
        }

        return BinaryTreeNode::basic()
            ->where('user_id', $user->id)
            ->where('sale_log_id', $saleLogId)
            ->exists();
    }

    /**
     * Left-most breadth-first search for the first node (starting at $root,
     * inclusive) that still has a free child slot. One query per level.
     */
    private function findAvailableSlot(BinaryTreeNode $root, $saleLogId): ?BinaryTreeNode
    {
        $frontier = [$root->id];
        $visited  = [$root->id => true];

        while (!empty($frontier)) {
            $children = BinaryTreeNode::where('sale_log_id', $saleLogId)
                ->whereIn('parent_node_id', $frontier)
                ->orderBy('position', 'asc')
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get(['id', 'parent_node_id'])
                ->groupBy('parent_node_id');

            $next = [];

            foreach ($frontier as $nodeId) {
                $kids = $children->get($nodeId, collect());

                if ($kids->count() < 2) {
                    return BinaryTreeNode::find($nodeId);
                }

                foreach ($kids as $kid) {
                    if (!isset($visited[$kid->id])) {
                        $visited[$kid->id] = true;
                        $next[] = $kid->id;
                    }
                }
            }

            $frontier = $next;
        }

        return null;
    }

    /**
     * Helper: next child position (1 = left, 2 = right).
     */
    private function getNextPosition($parentNodeId, $saleLogId): int
    {
        if (!$parentNodeId) {
            return 1;
        }

        $count = BinaryTreeNode::where('parent_node_id', $parentNodeId)
            ->where('sale_log_id', $saleLogId)
            ->count();

        if ($count == 0) {
            return 1;
        }

        if ($count == 1) {
            return 2;
        }

        throw new \Exception('Parent already has 2 children');
    }

    /**
     * Create the node row and compute its great_parent_id.
     */
    private function createNode(User $user, $saleLogId, ?BinaryTreeNode $parent, array $extra): BinaryTreeNode
    {
        $parentLevel = $parent ? $parent->level : -1;
        $position    = $this->getNextPosition($parent?->id, $saleLogId);

        $node = BinaryTreeNode::create(array_merge([
            'user_id'        => $user->id,
            'parent_id'      => $parent?->user_id,
            'parent_node_id' => $parent?->id,
            'level'          => $parentLevel + 1,
            'position'       => $position,
            'sale_log_id'    => $saleLogId,
            'created_at'     => now(),
        ], $extra));

        $greatParent = $node->getGreatParentNodeId();
        $node->great_parent_id = $greatParent?->id;
        $node->save();

        return $node;
    }

    /**
     * Completion check after a node was placed.
     * Re-use OFF: legacy great-parent rule. Re-use ON: 14 descendants rule.
     */
    private function handleCompletion(BinaryTreeNode $node): void
    {
        if (GeneralSetting::reuseEnabled()) {
            $this->completeAncestorsByDescendantCount($node);
            return;
        }

        $greatParent = $node->great_parent_id ? BinaryTreeNode::find($node->great_parent_id) : null;
        if (!empty($greatParent)) {
            $this->legacyCheckAndDeactivateParent($greatParent);
        }
    }

    /**
     * Legacy rule: great-parent completes when it has 8 great-grandchildren records.
     */
    private function legacyCheckAndDeactivateParent(BinaryTreeNode $greatParent): void
    {
        $descendantCount = BinaryTreeNode::where('great_parent_id', $greatParent->id)->count();

        if ($descendantCount == 8 && !$greatParent->is_super_prime) {
            if ($greatParent->status == BinaryTreeNode::ACTIVE_IN_TREE) {
                $this->markCompleted($greatParent, true);
            }
        }
    }

    /**
     * New rule: any of the 3 ancestors with >= 14 descendants (within 3 levels)
     * completes. Clones complete by the same rule but never get re-activated.
     */
    private function completeAncestorsByDescendantCount(BinaryTreeNode $node): void
    {
        $current = $node;

        for ($i = 1; $i <= 3; $i++) {
            if (empty($current->parent_node_id)) {
                break;
            }

            $ancestor = BinaryTreeNode::find($current->parent_node_id);
            if (!$ancestor) {
                break;
            }

            if ($ancestor->status == BinaryTreeNode::ACTIVE_IN_TREE
                && !optional($ancestor->user)->is_super_prime
                && $this->countDescendants($ancestor) >= BinaryTreeNode::COMPLETION_THRESHOLD) {
                // Only basic IDs notify; clones complete silently.
                $this->markCompleted($ancestor, !$ancestor->is_clone);
            }

            $current = $ancestor;
        }
    }

    /**
     * Descendants of a node within 3 levels (children, grandchildren, great-grandchildren).
     */
    private function countDescendants(BinaryTreeNode $node): int
    {
        $ids   = [$node->id];
        $total = 0;

        for ($level = 1; $level <= 3; $level++) {
            $ids = BinaryTreeNode::where('sale_log_id', $node->sale_log_id)
                ->whereIn('parent_node_id', $ids)
                ->pluck('id')
                ->all();

            if (empty($ids)) {
                break;
            }

            $total += count($ids);
        }

        return $total;
    }

    private function markCompleted(BinaryTreeNode $node, bool $notify): void
    {
        // Only status changes here (amber = pending to be inactive); the
        // update:user-sale-log cron later sets active_in_sale_log = 0 (red).
        $node->status = BinaryTreeNode::INACTIVE_IN_TREE;
        $node->save();

        if ($notify) {
            (new NotificationService())->create('sale_log_completed', $node->user_id, [
                'name'    => $node->user->name,
                'saleLog' => $node->saleLog->name,
            ], 0);
        }
    }
}
