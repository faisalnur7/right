<?php

namespace App\Http\Controllers;

use App\Models\BinaryTreeNode;
use App\Models\SaleLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TeamOrganogramController extends Controller
{
    /** Levels shown under the user's own node: 2 + 4 + 8 = 14 positions. */
    const DEPTH = 3;

    public function index(Request $request)
    {
        $user = auth()->user();

        abort_unless($user->user_affiliate_type == User::PRIME, 403);

        $tabs = SaleLog::orderBy('id')->get()->map(function (SaleLog $saleLog) use ($user) {
            // Root is always the user's current basic ID, never a clone.
            $root = BinaryTreeNode::basic()
                ->where('user_id', $user->id)
                ->where('sale_log_id', $saleLog->id)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->first();

            return [
                'sale_log'   => $saleLog,
                'activation' => $root ? (int) $root->activation_number : 0,
                'tree'       => $root ? $this->buildTree($root) : null,
            ];
        });

        $activeSaleLogId = (int) $request->query('sale_log_id');
        if (!$tabs->contains(fn ($tab) => $tab['sale_log']->id === $activeSaleLogId)) {
            $activeSaleLogId = $tabs->first()['sale_log']->id ?? null;
        }

        return view('prime_user.team_organogram.index', [
            'tabs'            => $tabs,
            'activeSaleLogId' => $activeSaleLogId,
        ]);
    }

    /**
     * Build the root node plus DEPTH levels below it (clones included),
     * in the array shape expected by admin.tree.partials.graph_node.
     */
    private function buildTree(BinaryTreeNode $root): array
    {
        // Load the subtree one level at a time.
        $levels = [collect([$root])];
        for ($depth = 1; $depth <= self::DEPTH; $depth++) {
            $parentIds = $levels[$depth - 1]->pluck('id');
            if ($parentIds->isEmpty()) {
                break;
            }
            $levels[$depth] = $this->childrenOf($parentIds, $root->sale_log_id);
        }

        $nodes = BinaryTreeNode::newModelInstance()->newCollection(collect($levels)->flatten(1)->all());
        $nodes->load('user.kyc');

        $childrenByParent = $nodes->where('id', '!=', $root->id)->groupBy('parent_node_id');

        // Bottom-level nodes still need their real child count for the status icon.
        $bottomIds = count($levels) > self::DEPTH ? $levels[self::DEPTH]->pluck('id') : collect();
        $bottomChildCounts = $bottomIds->isEmpty()
            ? collect()
            : BinaryTreeNode::whereIn('parent_node_id', $bottomIds)
                ->where('sale_log_id', $root->sale_log_id)
                ->selectRaw('parent_node_id, COUNT(*) as total')
                ->groupBy('parent_node_id')
                ->pluck('total', 'parent_node_id');

        return $this->formatNode($root, $root, $childrenByParent, $bottomChildCounts);
    }

    private function childrenOf(Collection $parentIds, $saleLogId): Collection
    {
        return BinaryTreeNode::whereIn('parent_node_id', $parentIds)
            ->where('sale_log_id', $saleLogId)
            ->orderBy('position')   // 1 = left, 2 = right
            ->orderBy('created_at')
            ->get();
    }

    private function formatNode(BinaryTreeNode $node, BinaryTreeNode $root, Collection $childrenByParent, Collection $bottomChildCounts): array
    {
        $children = $childrenByParent->get($node->id, collect());
        $photo    = $node->user?->kyc?->photo;

        return [
            'user'                     => $node->user,
            'user_id'                  => $node->user_id,
            'sale_log_id'              => $node->sale_log_id,
            'node_id'                  => $node->id,
            'parent_node_id'           => $node->parent_node_id,
            // Level relative to the viewer; their own upline position stays private.
            'level'                    => $node->level - $root->level,
            'position'                 => $node->id === $root->id ? null : $node->position,
            'activation_number'        => $node->activation_number,
            'is_active'                => $node->status,
            'is_active_in_sale_log'    => $node->active_in_sale_log,
            'is_clone'                 => (bool) $node->is_clone,
            'has_photo'                => $photo && file_exists(public_path('kyc/photo/' . $photo)),
            'immediate_children_count' => $children->count() ?: (int) $bottomChildCounts->get($node->id, 0),
            'children'                 => $children
                ->map(fn ($child) => $this->formatNode($child, $root, $childrenByParent, $bottomChildCounts))
                ->all(),
        ];
    }
}
