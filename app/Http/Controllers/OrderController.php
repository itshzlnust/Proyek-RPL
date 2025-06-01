<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'orderItems.treatment']);

        // Search by treatment name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('orderItems.treatment', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere(function($sub) use ($search) {
                      $sub->where('is_bundle', 1)
                          ->where('bundle_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by treatment if provided
        if ($request->has('treatment_id') && $request->treatment_id) {
            $treatment_id = $request->treatment_id;
            $query->whereHas('orderItems', function ($q) use ($treatment_id) {
                $q->where('treatment_id', $treatment_id);
            });
        }

        // Filter by date range if provided
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('order_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('order_date', '<=', $request->end_date);
        }

        $orders = $query->latest()->paginate(10);
        
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'data' => $orders
            ]);
        }
        
        $treatments = Treatment::all();
        
        return view('orders.index', compact('orders', 'treatments'));
    }

    public function create()
    {
        $customers = Customer::all();
        $treatments = Treatment::all();
        return view('orders.create', compact('customers', 'treatments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'status' => 'required|string',
            'treatments' => 'required|array',
            'treatments.*.treatment_id' => 'required|exists:treatments,id',
            'treatments.*.quantity' => 'required|integer|min:1',
            'treatments.*.price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Calculate total amount
            $total_amount = 0;
            foreach ($request->treatments as $item) {
                $total_amount += $item['quantity'] * $item['price'];
            }

            // Generate order code
            $order_code = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);

            // Create order
            $order = Order::create([
                'order_code' => $order_code,
                'customer_id' => $request->customer_id,
                'total_amount' => $total_amount,
                'order_date' => $request->order_date,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // Create order items
            foreach ($request->treatments as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'treatment_id' => $item['treatment_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price'],
                ]);
            }

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error creating order: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'orderItems.treatment']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['orderItems.treatment']);
        $customers = Customer::all();
        $treatments = Treatment::all();
        return view('orders.edit', compact('order', 'customers', 'treatments'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'status' => 'required|string',
            'treatments' => 'required|array',
            'treatments.*.treatment_id' => 'required|exists:treatments,id',
            'treatments.*.quantity' => 'required|integer|min:1',
            'treatments.*.price' => 'required|numeric|min:0',
            'treatments.*.id' => 'nullable|exists:order_items,id',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Calculate total amount
            $total_amount = 0;
            foreach ($request->treatments as $item) {
                $total_amount += $item['quantity'] * $item['price'];
            }

            // Update order
            $order->update([
                'customer_id' => $request->customer_id,
                'total_amount' => $total_amount,
                'order_date' => $request->order_date,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // Delete existing order items that are not in the request
            $itemIds = collect($request->treatments)->pluck('id')->filter()->toArray();
            $order->orderItems()->whereNotIn('id', $itemIds)->delete();

            // Update or create order items
            foreach ($request->treatments as $item) {
                if (isset($item['id'])) {
                    OrderItem::where('id', $item['id'])->update([
                        'treatment_id' => $item['treatment_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $item['quantity'] * $item['price'],
                    ]);
                } else {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'treatment_id' => $item['treatment_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $item['quantity'] * $item['price'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('orders.index')
                ->with('success', 'Order updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error updating order: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully');
    }
} 