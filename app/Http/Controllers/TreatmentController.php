<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TreatmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Treatment::query();
        
        // Search by name if search parameter is provided
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere(function($q) use ($search) {
                      $q->where('is_bundle', 1)
                        ->where('bundle_name', 'like', "%{$search}%");
                  });
        }
        
        $treatments = $query->latest()->paginate(10);
        
        if ($request->has('search')) {
            $treatments->appends(['search' => $request->search]);
        }
        
        return view('treatments.index', compact('treatments'));
    }

    public function create()
    {
        return view('treatments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_bundle' => 'boolean',
            'bundle_name' => 'nullable|string|max:255',
        ]);

        Treatment::create($request->all());

        return redirect()->route('treatments.index')
            ->with('success', 'Treatment created successfully.');
    }

    public function show(Treatment $treatment)
    {
        // Get monthly usage data for the specific treatment for the current year
        $currentYear = Carbon::now()->year;
        $usageData = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                DB::raw("CAST(strftime('%m', orders.order_date) AS INTEGER) as month"),
                DB::raw('SUM(order_items.quantity) as count')
            )
            ->where('order_items.treatment_id', $treatment->id)
            ->whereYear('orders.order_date', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->all();

        // Initialize usage array for all 12 months
        $treatmentUsage = [];
        for ($m = 1; $m <= 12; $m++) {
            $treatmentUsage[$m] = $usageData[$m] ?? 0;
        }

        // Prepare data for the chart (similar to dashboard)
        $usageMonthNames = collect(range(1, 12))->map(function ($month) {
            return Carbon::create()->month($month)->format('M');
        })->toArray();
        $usageValues = array_values($treatmentUsage);

        return view('treatments.show', compact('treatment', 'usageMonthNames', 'usageValues'));
    }

    public function edit(Treatment $treatment)
    {
        return view('treatments.edit', compact('treatment'));
    }

    public function update(Request $request, Treatment $treatment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_bundle' => 'boolean',
            'bundle_name' => 'nullable|string|max:255',
        ]);

        $treatment->update($request->all());

        return redirect()->route('treatments.index')
            ->with('success', 'Treatment updated successfully');
    }

    public function destroy(Treatment $treatment)
    {
        $treatment->delete();

        return redirect()->route('treatments.index')
            ->with('success', 'Treatment deleted successfully');
    }
} 