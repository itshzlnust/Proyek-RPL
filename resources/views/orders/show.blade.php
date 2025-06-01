@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-shopping-cart me-2"></i>Order Details</span>
        <div>
            <a href="{{ route('orders.invoice', $order) }}" class="btn btn-success" target="_blank">
                <i class="fas fa-file-invoice me-1"></i> Print Invoice
            </a>
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Order Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th style="width: 35%;">Order ID</th>
                                <td>{{ $order->order_code }}</td>
                            </tr>
                            <tr>
                                <th>Order Date</th>
                                <td>{{ $order->order_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'primary') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Total Amount</th>
                                <td class="fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td>{{ $order->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <th style="width: 35%;">Customer Code</th>
                                <td>{{ $order->customer->customer_code }}</td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{ $order->customer->name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $order->customer->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $order->customer->phone ?? '-' }}</td>
                            </tr>
                        </table>
                        <div class="mt-3">
                            <a href="{{ route('customers.show', $order->customer) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-user me-1"></i> View Customer
                            </a>
                        </div>
                    </div>
                </div>
                
                @if($order->notes)
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Notes</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $order->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Order Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th>Treatment</th>
                                <th>Type</th>
                                <th style="width: 10%;">Quantity</th>
                                <th style="width: 15%;">Price</th>
                                <th style="width: 15%;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->orderItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->treatment->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->treatment->is_bundle ? 'info' : 'primary' }}">
                                        @if($item->treatment->is_bundle)
                                            Bundle: {{ $item->treatment->bundle_name }}
                                        @else
                                            Individual
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No items found</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end">Total:</th>
                                <th>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 