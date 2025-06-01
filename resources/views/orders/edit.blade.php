@extends('layouts.app')

@section('title', 'Edit Order')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="fas fa-edit me-2"></i>Edit Order
    </div>
    <div class="card-body">
        <form action="{{ route('orders.update', $order) }}" method="POST" id="orderForm">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                    <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id', $order->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->customer_code }} - {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3">
                    <label for="order_date" class="form-label">Order Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('order_date') is-invalid @enderror" id="order_date" name="order_date" value="{{ old('order_date', $order->order_date->format('Y-m-d')) }}" required>
                    @error('order_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ old('status', $order->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status', $order->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Order Items</h5>
                        <button type="button" class="btn btn-sm btn-primary" id="addTreatmentBtn">
                            <i class="fas fa-plus"></i> Add Treatment
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="treatmentsTable">
                            <thead>
                                <tr>
                                    <th>Treatment</th>
                                    <th style="width: 150px;">Quantity</th>
                                    <th style="width: 200px;">Price (Rp)</th>
                                    <th style="width: 200px;">Subtotal (Rp)</th>
                                    <th style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="emptyRow" style="{{ $order->orderItems->count() > 0 ? 'display:none' : '' }}">
                                    <td colspan="5" class="text-center">No treatments added yet</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th id="totalAmount">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
                <button type="submit" class="btn btn-primary" id="saveOrderBtn">
                    <i class="fas fa-save me-1"></i> Update Order
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Treatment row template (hidden) -->
<template id="treatmentRowTemplate">
    <tr class="treatment-row">
        <td>
            <input type="hidden" name="treatments[{index}][id]" value="">
            <select class="form-select treatment-select" name="treatments[{index}][treatment_id]" required>
                <option value="">Select Treatment</option>
                @foreach($treatments as $treatment)
                    <option value="{{ $treatment->id }}" data-price="{{ $treatment->price }}">
                        {{ $treatment->name }} 
                        @if($treatment->is_bundle)
                            (Bundle: {{ $treatment->bundle_name }})
                        @endif
                        - Rp {{ number_format($treatment->price, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" class="form-control quantity-input" name="treatments[{index}][quantity]" min="1" value="1" required>
        </td>
        <td>
            <input type="number" class="form-control price-input" name="treatments[{index}][price]" min="0" step="1000" value="0" required>
        </td>
        <td>
            <span class="subtotal">Rp 0</span>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger remove-treatment-btn">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const treatmentRowTemplate = document.getElementById('treatmentRowTemplate');
        const treatmentsTable = document.getElementById('treatmentsTable');
        const emptyRow = document.getElementById('emptyRow');
        const addTreatmentBtn = document.getElementById('addTreatmentBtn');
        const totalAmountElement = document.getElementById('totalAmount');
        const orderForm = document.getElementById('orderForm');
        
        let treatmentIndex = {{ $order->orderItems->count() }};
        
        // Add treatment row
        addTreatmentBtn.addEventListener('click', function() {
            // Hide empty row
            emptyRow.style.display = 'none';
            
            // Clone template and replace {index} placeholder
            const clone = document.importNode(treatmentRowTemplate.content, true);
            const row = clone.querySelector('tr');
            
            row.innerHTML = row.innerHTML.replace(/{index}/g, treatmentIndex);
            
            // Append the new row
            treatmentsTable.querySelector('tbody').appendChild(row);
            
            // Setup event listeners for the new row
            setupRowEventListeners(row);
            
            // Increment index for the next row
            treatmentIndex++;
            
            // Update totals
            updateTotals();
        });
        
        // Setup event listeners for a row
        function setupRowEventListeners(row) {
            // Treatment select change
            const treatmentSelect = row.querySelector('.treatment-select');
            const priceInput = row.querySelector('.price-input');
            
            treatmentSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.dataset.price || 0;
                priceInput.value = price;
                updateRowSubtotal(row);
                updateTotals();
            });
            
            // Quantity and price input change
            const quantityInput = row.querySelector('.quantity-input');
            
            quantityInput.addEventListener('change', function() {
                updateRowSubtotal(row);
                updateTotals();
            });
            
            priceInput.addEventListener('change', function() {
                updateRowSubtotal(row);
                updateTotals();
            });
            
            // Remove button click
            const removeBtn = row.querySelector('.remove-treatment-btn');
            
            removeBtn.addEventListener('click', function() {
                row.remove();
                updateTotals();
                
                // Show empty row if no treatments
                const treatmentRows = treatmentsTable.querySelectorAll('.treatment-row');
                if (treatmentRows.length === 0) {
                    emptyRow.style.display = '';
                }
            });
        }
        
        // Update row subtotal
        function updateRowSubtotal(row) {
            const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const subtotal = quantity * price;
            
            row.querySelector('.subtotal').textContent = 'Rp ' + formatNumber(subtotal);
        }
        
        // Update totals
        function updateTotals() {
            let total = 0;
            const subtotalElements = treatmentsTable.querySelectorAll('.subtotal');
            
            subtotalElements.forEach(function(element) {
                const subtotalText = element.textContent.replace('Rp ', '').replace(/\./g, '');
                const subtotal = parseFloat(subtotalText) || 0;
                total += subtotal;
            });
            
            totalAmountElement.textContent = 'Rp ' + formatNumber(total);
        }
        
        // Format number with thousands separator
        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }
        
        // Form submit validation
        orderForm.addEventListener('submit', function(e) {
            const treatmentRows = treatmentsTable.querySelectorAll('.treatment-row');
            
            if (treatmentRows.length === 0) {
                e.preventDefault();
                alert('Please add at least one treatment to the order.');
                return false;
            }
            
            return true;
        });
        
        // Add existing order items or rows from old input
        @if(old('treatments'))
            // Load from old input (after validation error)
            @foreach(old('treatments') as $index => $treatment)
                const clone{{ $index }} = document.importNode(treatmentRowTemplate.content, true);
                const row{{ $index }} = clone{{ $index }}.querySelector('tr');
                
                row{{ $index }}.innerHTML = row{{ $index }}.innerHTML.replace(/{index}/g, {{ $index }});
                
                treatmentsTable.querySelector('tbody').appendChild(row{{ $index }});
                
                // Set the values
                @if(isset($treatment['id']))
                    row{{ $index }}.querySelector('input[name="treatments[{{ $index }}][id]"]').value = '{{ $treatment['id'] }}';
                @endif
                
                const treatmentSelect{{ $index }} = row{{ $index }}.querySelector('.treatment-select');
                treatmentSelect{{ $index }}.value = '{{ $treatment['treatment_id'] }}';
                
                const quantityInput{{ $index }} = row{{ $index }}.querySelector('.quantity-input');
                quantityInput{{ $index }}.value = '{{ $treatment['quantity'] }}';
                
                const priceInput{{ $index }} = row{{ $index }}.querySelector('.price-input');
                priceInput{{ $index }}.value = '{{ $treatment['price'] }}';
                
                // Setup event listeners and update subtotal
                setupRowEventListeners(row{{ $index }});
                updateRowSubtotal(row{{ $index }});
            @endforeach
        @else
            // Load from existing order items
            @foreach($order->orderItems as $index => $item)
                const clone{{ $index }} = document.importNode(treatmentRowTemplate.content, true);
                const row{{ $index }} = clone{{ $index }}.querySelector('tr');
                
                row{{ $index }}.innerHTML = row{{ $index }}.innerHTML.replace(/{index}/g, {{ $index }});
                
                treatmentsTable.querySelector('tbody').appendChild(row{{ $index }});
                
                // Set the values
                row{{ $index }}.querySelector('input[name="treatments[{{ $index }}][id]"]').value = '{{ $item->id }}';
                
                const treatmentSelect{{ $index }} = row{{ $index }}.querySelector('.treatment-select');
                treatmentSelect{{ $index }}.value = '{{ $item->treatment_id }}';
                
                const quantityInput{{ $index }} = row{{ $index }}.querySelector('.quantity-input');
                quantityInput{{ $index }}.value = '{{ $item->quantity }}';
                
                const priceInput{{ $index }} = row{{ $index }}.querySelector('.price-input');
                priceInput{{ $index }}.value = '{{ $item->price }}';
                
                // Setup event listeners and update subtotal
                setupRowEventListeners(row{{ $index }});
                updateRowSubtotal(row{{ $index }});
            @endforeach
        @endif
        
        // Update totals
        updateTotals();
    });
</script>
@endsection 