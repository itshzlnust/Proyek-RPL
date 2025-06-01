@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-user me-2"></i>Customer Details</span>
        <div>
            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th style="width: 30%;">Customer ID</th>
                                <td>{{ $customer->id }}</td>
                            </tr>
                            <tr>
                                <th>Customer Code</th>
                                <td>{{ $customer->customer_code }}</td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{ $customer->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $customer->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $customer->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Registered Since</th>
                                <td>{{ $customer->created_at->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Order Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Orders</span>
                                <span class="fw-bold">{{ $customer->orders->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Spent</span>
                                <span class="fw-bold">Rp {{ number_format($customer->orders->sum('total_amount'), 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Last Order</span>
                                <span class="fw-bold">
                                    @if($customer->orders->count() > 0)
                                        {{ $customer->orders->sortByDesc('order_date')->first()->order_date->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        <div class="chart-container" style="height: 200px;">
                            <canvas id="customerOrdersChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Recent Orders</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->orders->sortByDesc('order_date')->take(5) as $order)
                            <tr>
                                <td>{{ $order->order_code }}</td>
                                <td>{{ $order->order_date->format('d M Y') }}</td>
                                <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'primary') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No orders found for this customer</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('orders.create') }}?customer_id={{ $customer->id }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Create New Order
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Customer orders chart
    const ctx = document.getElementById('customerOrdersChart');
    
    // Get last 6 months
    const months = [];
    const orderData = [0, 0, 0, 0, 0, 0]; // Default empty data
    
    const today = new Date();
    for (let i = 5; i >= 0; i--) {
        const month = new Date(today.getFullYear(), today.getMonth() - i, 1);
        months.push(month.toLocaleString('default', { month: 'short' }));
    }
    
    // In a real app, you would populate this with actual data from the backend
    // Here we're just using sample data for demonstration
    @if($customer->orders->count() > 0)
        // Some sample data - in a real app this would come from your controller
        orderData[1] = 1;
        orderData[3] = 2;
        orderData[5] = 1;
    @endif
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Orders',
                data: orderData,
                backgroundColor: '#8a2be2',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endsection 