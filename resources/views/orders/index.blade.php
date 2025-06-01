@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-filter me-1"></i> Filter Orders
    </div>
    <div class="card-body">
        <form action="{{ route('orders.index') }}" method="GET">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">Treatment Name</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Search by treatment name" value="{{ request('search') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="treatment_id" class="form-label">Treatment</label>
                    <select class="form-select" id="treatment_id" name="treatment_id">
                        <option value="">All Treatments</option>
                        @foreach($treatments as $treatment)
                            <option value="{{ $treatment->id }}" {{ request('treatment_id') == $treatment->id ? 'selected' : '' }}>
                                {{ $treatment->name }} 
                                @if($treatment->is_bundle)
                                    (Bundle: {{ $treatment->bundle_name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end mb-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-shopping-cart me-2"></i>All Orders</span>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Order
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Treatments</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_code }}</td>
                        <td>{{ $order->customer->name }}</td>
                        <td>
                            <ul class="list-unstyled mb-0">
                                @foreach($order->orderItems as $item)
                                    <li>
                                        {{ $item->treatment->name }}
                                        @if($item->treatment->is_bundle)
                                            <span class="badge bg-info">Bundle</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ $order->order_date->format('d M Y') }}</td>
                        <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'primary') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('orders.invoice', $order) }}" class="btn btn-sm btn-success" target="_blank">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                                <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No orders found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection 