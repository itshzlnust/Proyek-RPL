@extends('layouts.app')

@section('title', 'Edit Treatment')

@section('content')
<div class="card">
    <div class="card-header">
        <i class="fas fa-edit me-2"></i>Edit Treatment
    </div>
    <div class="card-body">
        <form action="{{ route('treatments.update', $treatment) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Treatment Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $treatment->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="price" class="form-label">Price (Rp) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" min="0" step="1000" value="{{ old('price', $treatment->price) }}" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $treatment->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_bundle" name="is_bundle" value="1" {{ old('is_bundle', $treatment->is_bundle) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_bundle">
                        This is a bundle treatment
                    </label>
                </div>
            </div>
            
            <div class="mb-3" id="bundle_name_field" style="{{ old('is_bundle', $treatment->is_bundle) ? '' : 'display:none;' }}">
                <label for="bundle_name" class="form-label">Bundle Name</label>
                <input type="text" class="form-control @error('bundle_name') is-invalid @enderror" id="bundle_name" name="bundle_name" value="{{ old('bundle_name', $treatment->bundle_name) }}">
                @error('bundle_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('treatments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update Treatment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Show/hide bundle name field based on checkbox
    document.getElementById('is_bundle').addEventListener('change', function() {
        const bundleNameField = document.getElementById('bundle_name_field');
        bundleNameField.style.display = this.checked ? 'block' : 'none';
    });
</script>
@endsection 