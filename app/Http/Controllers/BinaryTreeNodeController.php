<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\BinaryTreeNode;
use App\Models\UseProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SaleLog;
use App\Models\UserSaleLogUnit;
use App\Models\PrimeTransaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\BinaryTreeService;
use App\Services\CommissionDistributionService;

class BinaryTreeNodeController extends Controller
{
    public function adminUseProduct(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($request->user_id);
            $this->handleProductUsage($user, $request);

            DB::commit();
            session()->flash('success', 'Product used and user placed in tree');
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage() ?: 'Something went wrong. Please try again.');
        }

        return redirect()->route('adminUserList');
    }

    public function useProduct(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $this->handleProductUsage($user, $request);

            DB::commit();
            session()->flash('success', 'Product used and user placed in tree');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage() ?: 'Something went wrong. Please try again.');
        }

        return redirect()->back();
    }

    public function adminUserList(Request $request)
    {
        $perPage   = $request->per_page ?: 10;
        $saleLogId = $request->sale_log_id ?: null;

        $baseQuery = User::query()
            ->where('users.prime_verified', User::PRIME_VERIFIED_STATUS_COMPLETED)
            ->where('users.is_active', User::USER_PACKAGE_ACTIVE);

        $usersQuery = (clone $baseQuery)
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
            'per_page'      => $perPage,
            'totalUsers'    => (clone $baseQuery)->count(),
            'activeUsers'   => 0,
            'inactiveUsers' => 0,
            'users'         => $usersQuery,
            'saleLogs'      => SaleLog::all(),
        ];

        if ($saleLogId) {
            $statusCount = function (int $status) use ($baseQuery, $saleLogId) {
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

        return view('admin.user.index', $data);
    }

    public function adminUserStock(Request $request, $userId){
        $saleLogId = $request->sale_log_id ?? SaleLog::first()->id;
        $data['saleLogs'] = SaleLog::all();

        // Build base stock logs query
        $stockLogsQuery = OrderItem::whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', Order::COMPLETED);
            })
            ->selectRaw('product_id, sale_log_id, SUM(quantity) as total_quantity')
            ->with('product', 'sale_log')
            ->groupBy('product_id', 'sale_log_id');

        // Add condition only if sale_log_id is provided
        if (!empty($saleLogId)) {
            $stockLogsQuery->where('sale_log_id', $saleLogId);
        }

        $data['stockLogs'] = $stockLogs = $stockLogsQuery->get();

        foreach ($stockLogs as $log) {
            $usedQuantity = UseProduct::where(function ($query) use ($userId) {
                    $query->where('from_user_id', $userId)
                        ->orWhere('user_id', $userId);
                })
                ->where('product_id', $log->product_id)
                ->where('sale_log_id', $log->sale_log_id)
                ->sum('quantity');

            $log->total_quantity = max(0, $log->total_quantity - $usedQuantity);
        }

        $data['user'] = User::find($userId);
        return view('admin.user.user_stock', $data);
    }

    public function countDescendants($userId, $saleLogId)
    {
        // Load all nodes for this sale_log_id once
        $nodes = BinaryTreeNode::where('sale_log_id', $saleLogId)->get(['user_id', 'parent_id']);

        // Build adjacency list
        $childrenMap = [];
        foreach ($nodes as $node) {
            if ($node->parent_id !== null) {
                $childrenMap[$node->parent_id][] = $node->user_id;
            }
        }

        $count   = 0;
        $queue   = collect([$userId]);
        $visited = [];

        while ($queue->isNotEmpty()) {
            $current = $queue->shift();

            // Skip if already visited
            if (isset($visited[$current])) {
                continue;
            }
            $visited[$current] = true;

            if (!empty($childrenMap[$current])) {
                foreach ($childrenMap[$current] as $childId) {
                    if (!isset($visited[$childId])) {
                        $queue->push($childId);
                        $count++; // count each unique descendant
                    }
                }
            }
        }

        return $count;
    }

    public function getDescendantsWithLevels($userId, $saleLogId, $baseLevel = null)
    {
        $descendants = [];

        $startingNode = BinaryTreeNode::where('user_id', $userId)
            ->where('sale_log_id', $saleLogId)
            ->latest()
            ->first();

        if (!$startingNode) return [];

        if (is_null($baseLevel)) {
            $baseLevel = $startingNode->level;
        }

        $children = BinaryTreeNode::where('parent_id', $userId)
            ->where('sale_log_id', $saleLogId)
            ->get();

        foreach ($children as $child) {
            $relativeLevel = $child->level - $baseLevel;

            $descendants[] = [
                'user_id' => $child->user_id,
                'absolute_level' => $child->level,
                'relative_level' => $relativeLevel,
                'activation_number' => $child->activation_number,
            ];

            $descendants = array_merge($descendants, $this->getDescendantsWithLevels($child->user_id, $saleLogId, $baseLevel));
        }

        return $descendants;
    }

    public function adminTreeSelect()
    {
        $saleLogs = SaleLog::all();
        return view('admin.tree.select_sale_log', compact('saleLogs'));
    }

    public function adminTreeView(Request $request)
    {
        $saleLogId = $request->sale_log_id ?? SaleLog::first()->id;
        $affiliateId = $request->affiliate_id;

        $count = 0;
        $limit = 1000;
        $tree = [];
        $saleLogs = SaleLog::all();
        
        // Get tree statistics
        $treeStats = $this->getTreeStats($saleLogId);

        if ($affiliateId) {
            // If filtering by affiliate_id
            $user = User::whereHas('kyc', function ($q) use ($affiliateId) {
                $q->where('affiliate_id', $affiliateId);
            })->first();

            if (!$user) {
                return back()->with('error', 'Affiliate ID not found.');
            }

            $rootNode = BinaryTreeNode::where('user_id', $user->id)
                ->where('sale_log_id', $saleLogId)
                ->latest()
                ->first();

            if (!$rootNode) {
                return back()->with('error', 'User not found in tree for this Sale Log.');
            }

            $tree[] = $this->buildTree($rootNode, $saleLogId);
        } else {
            // Default root-based tree
            $roots = BinaryTreeNode::whereNull('parent_id')
                ->where('sale_log_id', $saleLogId)
                ->orderBy('created_at', 'asc') // Ensure consistent ordering
                ->get();

            foreach ($roots as $root) {
                if ($count >= $limit) break;

                $node = $this->buildTree($root, $saleLogId);
                if ($node['treeNode']) {
                    $tree[] = $node['treeNode'];
                }
            }
        }

        return view('admin.tree.view_tree', [
            'tree' => $tree,
            'saleLogId' => $saleLogId,
            'affiliateId' => $affiliateId,
            'saleLogs' => $saleLogs,
            'treeStats' => $treeStats, // Add tree statistics
            'count' => $node['count'],
            'limit' => $limit
        ]);
    }

    public function generateNode($node, $saleLogId, $growDeeper = true){

        $rootNode = BinaryTreeNode::find($node);

        $user = User::with(['kyc', 'saleLogUnits' => function ($q) use ($saleLogId) {
            $q->where('sale_log_id', $saleLogId);
        }])->find($rootNode->user_id);
        
        $photo = $user->kyc->photo ?? null;
        $hasPhoto = $photo && file_exists(public_path('kyc/photo/' . $photo));
        $descendantCount = $this->countDescendants($user->id, $saleLogId);
        $children = $this->getChildrenInOrder($rootNode->id, $saleLogId);
        $immediateChildrenCount = $children->count();
        $isActive = $rootNode->active_in_sale_log;

        $treeNode = [
            'user' => $user,
            'user_id' => $rootNode->user_id,
            'sale_log_id' => $saleLogId,
            'node_id' => $rootNode->id,
            'parent_node_id' => $rootNode->parent_node_id,
            'level' => $rootNode->level,
            'position' => $rootNode->position ?? null, // Include position info
            'activation_number' => $rootNode->activation_number,
            'is_active' => $rootNode->status,
            'is_active_in_sale_log' => $isActive,
            'has_photo' => $hasPhoto,
            'descendant_count' => $descendantCount,
            'immediate_children_count' => $immediateChildrenCount,
            'children_ids' => $children->pluck('user_id')->toArray(),
            'children' => [],
        ];

        if($growDeeper){
            foreach ($children as $child) {
                $treeNode['children'][] = $child;
            }
        }

        return $treeNode;
    }

    public function buildTree($node, $saleLogId)
    {
        $count = 1;
        $treeNode = $this->generateNode($node->id, $saleLogId);

        $count += count($treeNode['children']);
        // First level children
        if (!empty($treeNode['children'][0])) {
            $treeNode['children'][0] = $this->generateNode($treeNode['children'][0]->id, $saleLogId);
            $count += count($treeNode['children'][0]['children']);
            // Second level children for left node
            if (!empty($treeNode['children'][0]['children'][0])) {
                $treeNode['children'][0]['children'][0] = $this->generateNode($treeNode['children'][0]['children'][0]->id, $saleLogId);
                $count += count($treeNode['children'][0]['children'][0]['children']);

                // Third level children for left->left
                if (!empty($treeNode['children'][0]['children'][0]['children'][0])) {
                    $treeNode['children'][0]['children'][0]['children'][0] = $this->generateNode($treeNode['children'][0]['children'][0]['children'][0]->id, $saleLogId, false);
                }
                if (!empty($treeNode['children'][0]['children'][0]['children'][1])) {
                    $treeNode['children'][0]['children'][0]['children'][1] = $this->generateNode($treeNode['children'][0]['children'][0]['children'][1]->id, $saleLogId, false);
                }
            }
            if (!empty($treeNode['children'][0]['children'][1])) {
                $treeNode['children'][0]['children'][1] = $this->generateNode($treeNode['children'][0]['children'][1]->id, $saleLogId);
                $count += count($treeNode['children'][0]['children'][1]['children']);
                // Third level children for left->right
                if (!empty($treeNode['children'][0]['children'][1]['children'][0])) {
                    $treeNode['children'][0]['children'][1]['children'][0] = $this->generateNode($treeNode['children'][0]['children'][1]['children'][0]->id, $saleLogId, false);
                }
                if (!empty($treeNode['children'][0]['children'][1]['children'][1])) {
                    $treeNode['children'][0]['children'][1]['children'][1] = $this->generateNode($treeNode['children'][0]['children'][1]['children'][1]->id, $saleLogId, false);
                }
            }
        }

        if (!empty($treeNode['children'][1])) {
            $treeNode['children'][1] = $this->generateNode($treeNode['children'][1]->id, $saleLogId);
            $count += count($treeNode['children'][1]['children']);
            // Second level children for right node
            if (!empty($treeNode['children'][1]['children'][0])) {
                $treeNode['children'][1]['children'][0] = $this->generateNode($treeNode['children'][1]['children'][0]->id, $saleLogId);
                $count += count($treeNode['children'][1]['children'][0]['children'] );

                // Third level children for right->left
                if (!empty($treeNode['children'][1]['children'][0]['children'][0])) {
                    $treeNode['children'][1]['children'][0]['children'][0] = $this->generateNode($treeNode['children'][1]['children'][0]['children'][0]->id, $saleLogId, false);
                }
                if (!empty($treeNode['children'][1]['children'][0]['children'][1])) {
                    $treeNode['children'][1]['children'][0]['children'][1] = $this->generateNode($treeNode['children'][1]['children'][0]['children'][1]->id, $saleLogId, false);
                }
            }
            if (!empty($treeNode['children'][1]['children'][1])) {
                $treeNode['children'][1]['children'][1] = $this->generateNode($treeNode['children'][1]['children'][1]->id, $saleLogId);
                $count += count($treeNode['children'][1]['children'][1]['children'] );

                // Third level children for right->right
                if (!empty($treeNode['children'][1]['children'][1]['children'][0])) {
                    $treeNode['children'][1]['children'][1]['children'][0] = $this->generateNode($treeNode['children'][1]['children'][1]['children'][0]->id, $saleLogId, false);
                }
                if (!empty($treeNode['children'][1]['children'][1]['children'][1])) {
                    $treeNode['children'][1]['children'][1]['children'][1] = $this->generateNode($treeNode['children'][1]['children'][1]['children'][1]->id, $saleLogId, false);
                }
            }
        }

        return ['treeNode' => $treeNode, 'count'=>$count];
    }

    public function getChildrenInOrder($parentId, $saleLogId)
    {
        return BinaryTreeNode::where('parent_node_id', $parentId)
            ->where('sale_log_id', $saleLogId)
            ->orderBy('position', 'asc') // 1 = left, 2 = right
            ->orderBy('created_at', 'asc') // Fallback to creation time for consistency
            ->get();
    }

    public function getTreeStats($saleLogId)
    {
        $totalNodes = BinaryTreeNode::where('sale_log_id', $saleLogId)->count();
        $activeNodes = BinaryTreeNode::where('sale_log_id', $saleLogId)
            ->where('status',1)
            ->count();

        $maxLevel = BinaryTreeNode::where('sale_log_id', $saleLogId)->max('level') ?? 0;
        
        $levelCounts = BinaryTreeNode::where('sale_log_id', $saleLogId)
            ->selectRaw('level, COUNT(*) as count')
            ->groupBy('level')
            ->orderBy('level')
            ->get()
            ->pluck('count', 'level')
            ->toArray();

        return [
            'total_nodes' => $totalNodes,
            'active_nodes' => $activeNodes,
            'max_level' => $maxLevel,
            'level_counts' => $levelCounts,
        ];
    }


    public function adminTreeFromUser($userId, $nodeId, Request $request)
    {
        $saleLogId = $request->sale_log_id;
        $rootNode = BinaryTreeNode::find($nodeId);

        if (!$rootNode) {
            return back()->with('error', 'User not found in tree for this Sale Log.');
        }

        $count = 0;
        $limit = 32;

        $tree = $this->buildTree($rootNode, $saleLogId);
        $saleLogs = SaleLog::all();

        return view('admin.tree.view_tree', [
            'tree' => [$tree['treeNode']],
            'saleLogId' => $saleLogId,
            'saleLogs' => $saleLogs,
            'affiliateId' => null,
            'count' => $tree['count'],
            'limit' => $limit
        ]);
    }


    public function adminTreeFilter(Request $request)
    {
        $saleLogId = $request->input('sale_log_id');
        $affiliateId = $request->input('affiliate_id');
        $limit = 32;
        $count = 0;

        $saleLogs = SaleLog::all();
        $treeStats = $this->getTreeStats($saleLogId);

        // If affiliate ID provided, find user and build from their node
        if ($affiliateId) {
            $user = User::whereHas('kyc', function ($q) use ($affiliateId) {
                $q->where('affiliate_id', $affiliateId);
            })->first();

            if (!$user) {
                return back()->with('error', 'Affiliate ID not found.');
            }

            $rootNode = BinaryTreeNode::where('user_id', $user->id)
                ->where('sale_log_id', $saleLogId)
                ->latest()
                ->first();

            if (!$rootNode) {
                return back()->with('error', 'User not found in tree for this Sale Log.');
            }

            $treeData = $this->buildTree($rootNode, $saleLogId);
            // dd($treeData);
            $tree = [$treeData['treeNode']];
            $count = $treeData['count'];

        } else {
            // Default: show tree from all roots
            $roots = BinaryTreeNode::whereNull('parent_id')
                ->where('sale_log_id', $saleLogId)
                ->orderBy('created_at', 'asc') // Consistent ordering
                ->get();

            $treeData = [];
            foreach ($roots as $root) {
                $built = $this->buildTree($root, $saleLogId, $count, $limit);
                if ($built) {
                    $treeData[] = $built['treeNode'];
                    $count = $built['count'];
                }
            }
            $tree = $treeData;
        }


        return view('admin.tree.view_tree', compact('tree', 'saleLogId', 'affiliateId', 'count', 'limit', 'saleLogs', 'treeStats'));
    }

    public function handleProductUsage(User $user, Request $request)
    {
        return DB::transaction(function () use ($user, $request) {

            // ✅ 1. Fetch Product
            $product = Product::find($request->product_id);
            if (!$product) {
                throw new \Exception('Product not found.');
            }

            // ✅ 2. Validate Sale Log relation
            $saleLog = $product->saleLogs->firstWhere('id', $request->sale_log_id);
            if (!$saleLog || !$saleLog->pivot) {
                throw new \Exception('Invalid or missing sale log for this product.');
            }

            // ✅ 3. Create UseProduct record
            $useProductData = [
                'user_id'       => $user->id,
                'product_id'    => $product->id,
                'sale_log_id'   => $saleLog->id,
                'quantity'      => $request->quantity ?? 1,
                'price'         => $product->price,
                'total'         => $product->price * ($request->quantity ?? 1),
                'status'        => UseProduct::USED_PRODUCT,
                'used_at'       => now(),
                'remarks'       => $request->remarks,
                'validity'      => $saleLog->pivot->unit,
                'unit_per_day'  => $saleLog->pivot->price,
            ];

            $useProduct = UseProduct::create($useProductData);

            // ✅ 4. Update or Create UserSaleLogUnit
            $logUnit = UserSaleLogUnit::firstOrCreate([
                'user_id'     => $user->id,
                'sale_log_id' => $saleLog->id,
            ]);

            $unitCount = $saleLog->pivot->unit ?? 0;
            $logUnit->increment('remaining_units', $unitCount);
            $logUnit->is_active = $logUnit->remaining_units > 0 ? 1 : 0;
            $logUnit->save();

            // ✅ 5. Place user in binary tree (only first activation)
            $alreadyPlaced = BinaryTreeNode::where('user_id', $user->id)
                ->where('sale_log_id', $saleLog->id)
                ->where('status', BinaryTreeNode::ACTIVE_IN_TREE)
                ->exists();

            $node = null;
            if (!$alreadyPlaced) {
                $referrer = $user->referenceUser ?? null;

                $binaryTreeService = new BinaryTreeService();
                $node = $binaryTreeService->insertIntoBinaryTree($user, $saleLog->id, $referrer);
            }

            // ✅ Step 6: Distribute Commissions
            $commissionService = new CommissionDistributionService();
            $commissionService->distributeAllCommissions(
                $node,
                $user,
                $saleLog->id,
                $product->total_use_commission ?? 0,
                $product->associate_commission ?? 0
            );

            return $useProduct;
        });
    }

}