<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get treatment count
        $treatmentCount = Treatment::count();
        
        // Get customer count
        $customerCount = Customer::count();
        
        // Get order count
        $orderCount = Order::count();
        
        // Get total revenue
        $totalRevenue = Order::sum('total_amount');
        
        // Get recent orders
        $recentOrders = Order::with('customer')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get treatments data for chart (most popular treatments)
        $popularTreatments = DB::table('order_items')
            ->join('treatments', 'order_items.treatment_id', '=', 'treatments.id')
            ->select('treatments.name', DB::raw('SUM(order_items.quantity) as total_count'))
            ->groupBy('treatments.name')
            ->orderBy('total_count', 'desc')
            ->take(5)
            ->get();
        
        // Get monthly revenue for chart - Ensure all 12 months are present
        $currentYear = Carbon::now()->year;
        $revenueData = Order::select(
            DB::raw("CAST(strftime('%m', order_date) AS INTEGER) as month"),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereYear('order_date', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->all();

        // Initialize monthly revenue array for all 12 months
        $monthlyRevenue = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyRevenue[$m] = $revenueData[$m] ?? 0;
        }

        // Pass month names and revenue values separately for easier JS handling
        $monthNames = collect(range(1, 12))->map(function ($month) {
            return Carbon::create()->month($month)->format('M');
        })->toArray();
        
        $revenueValues = array_values($monthlyRevenue);

        return view('dashboard', compact(
            'treatmentCount',
            'customerCount',
            'orderCount',
            'totalRevenue',
            'recentOrders',
            'popularTreatments',
            'monthNames',
            'revenueValues'
        ));
    }
} 