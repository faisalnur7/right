<?php

namespace App\Http\Controllers\Admin;

use App\Models\SaleLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\BinaryTreeNode;
use App\Models\PackageUser;
use App\Models\PrimeTransaction;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {

        $date = Carbon::today();

        // Todays sale part
        $data['todayTotalSale'] = Order::whereDate('created_at', $date)->sum('subtotal');
        $todayBySaleLog = SaleLog::select(
                            'sale_logs.id',
                            'sale_logs.name',
                            DB::raw('COALESCE(SUM(order_items.subtotal), 0) as total_subtotal')
                        )
                        ->leftJoin('order_items', function($join) use ($date) {
                            $join->on('sale_logs.id', '=', 'order_items.sale_log_id')
                                ->whereDate('order_items.created_at', $date);
                        })->groupBy('sale_logs.id', 'sale_logs.name')->get();

        $data['todayBySaleLog'] = $todayBySaleLog;

        // ✅ Monthly Sales cart (current year, grouped by Sale Log & Month)
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        $rawMonthlySales = OrderItem::select(
                                'sale_log_id',
                                DB::raw('MONTH(created_at) as month'),
                                DB::raw('SUM(subtotal) as total')
                            )->whereBetween('created_at', [$startOfYear, $endOfYear])->groupBy('sale_log_id', 'month')->get();

        // Prepare months (Jan–Dec)
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create()->month($i)->format('M');
        }

        // Get all sale logs
        $saleLogs = SaleLog::pluck('name', 'id');

        // Users per sale log (distinct user_id) from binary_tree_nodes
        $usersBySaleLog = BinaryTreeNode::select('sale_log_id', DB::raw('COUNT(DISTINCT user_id) as total'))->groupBy('sale_log_id')->pluck('total', 'sale_log_id')->toArray();
        // Build dynamic array
        $usersData = [];

        $monthlySales = [
            'months' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            'logs' => [],
            'colors' => [],
        ];

        $defaultColors = [
            'PB' => ['border' => '#3b82f6', 'background' => 'rgba(59,130,246,0.6)'],
            'PS' => ['border' => '#10b981', 'background' => 'rgba(16,185,129,0.6)'],
            'PE' => ['border' => '#f59e0b', 'background' => 'rgba(245,158,11,0.6)'],
        ];

        $usersData = [];

        foreach ($saleLogs as $id => $name) {
            $logName = strtoupper($name);
            
            // Prepare 12 months of sales for this sale log
            $monthlyData = array_fill(0, 12, 0);

            foreach ($rawMonthlySales->where('sale_log_id', $id) as $row) {
                $monthIndex = $row->month - 1; // month column: 1-12
                $monthlyData[$monthIndex] = $row->total;
            }

            $monthlySales['logs'][$logName] = $monthlyData;
            $monthlySales['colors'][$logName] = $defaultColors[$logName] ?? ['border' => '#6b7280', 'background' => 'rgba(107,114,128,0.6)'];

            // Users count by sale log
            $usersData[strtolower($name)] = $usersBySaleLog[$id] ?? 0;
        }

        $data['monthlySales'] = $monthlySales;

        // Total users with prime_verified = 6
        $totalUsers = User::where('prime_verified', 6)->count();
        $data['todaysUsers'] = User::whereDate('updated_at', $date)->where('prime_verified', 6)->count() ?? 0;

        $data['adminSubscriptionIncome'] = PackageUser::query()->where('status',1)
                                                    ->whereDate('created_at', $date)
                                                    ->sum('amount');
        // Active users (distinct user_id) from binary_tree_nodes where status = 1
        $activeUsers = BinaryTreeNode::where('status', 1)->distinct('user_id')->count('user_id');

        $data['users'] = [
            'total' => $totalUsers,
            'active' => $activeUsers,
        ] + $usersData;

        
        // Get leads count for the date
        $rawLeads = BinaryTreeNode::whereDate('created_at', $date)->select('sale_log_id', DB::raw('COUNT(*) as total'))->groupBy('sale_log_id')->pluck('total', 'sale_log_id'); // [sale_log_id => total]

        $leads = [];
        foreach ($saleLogs as $id => $name) {
            $leads[strtolower($name)] = $rawLeads[$id] ?? 0;
        }
        
        $data['leads'] = $leads;

        // Commissions grouped
        $commissionSums = PrimeTransaction::whereDate('created_at', $date)->select('type', DB::raw('SUM(amount_in) as total'))->groupBy('type')->pluck('total', 'type')->toArray();

        $data['commissions'] = [
            'associate'    => $commissionSums[PrimeTransaction::ASSOCIATE_COMMISSION] ?? 0,
            'subscription' => ($commissionSums[PrimeTransaction::DIRECT_SUBSCRIPTION_COMMISSION] ?? 0)
                            + ($commissionSums[PrimeTransaction::INDIRECT_SUBSCRIPTION_COMMISSION] ?? 0),
            'affiliate'    => ($commissionSums[PrimeTransaction::DIRECT_PRODUCT_PURCHASE_COMMISSION] ?? 0)
                            + ($commissionSums[PrimeTransaction::INDIRECT_PRODUCT_PURCHASE_COMMISSION] ?? 0),
            'leads'        => ($commissionSums[PrimeTransaction::DIRECT_PRODUCT_USE_COMMISSION] ?? 0)
                            + ($commissionSums[PrimeTransaction::INDIRECT_PRODUCT_USE_COMMISSION] ?? 0),
        ];

        return view('admin.dashboard', $data);
    }
}
