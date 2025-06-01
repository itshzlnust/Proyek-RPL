@extends('layouts.app')

@section('title', 'Treatments')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-filter me-1"></i> Filter Treatments
    </div>
    <div class="card-body">
        <form action="{{ route('treatments.index') }}" method="GET">
            <div class="row">
                <div class="col-md-10 mb-3">
                    <label for="search" class="form-label">Treatment Name</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Search treatments..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end mb-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-spa me-2"></i>All Treatments</span>
        <a href="{{ route('treatments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Treatment
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Type</th>
                        <th>Bundle Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($treatments as $treatment)
                    <tr>
                        <td>{{ $treatment->id }}</td>
                        <td>{{ $treatment->name }}</td>
                        <td>Rp {{ number_format($treatment->price, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-{{ $treatment->is_bundle ? 'info' : 'primary' }}">
                                {{ $treatment->is_bundle ? 'Bundle' : 'Individual' }}
                            </span>
                        </td>
                        <td>{{ $treatment->is_bundle ? $treatment->bundle_name : '-' }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('treatments.show', $treatment) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('treatments.edit', $treatment) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('treatments.destroy', $treatment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this treatment?');">
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
                        <td colspan="6" class="text-center">No treatments found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $treatments->links() }}
        </div>
    </div>
</div>
@endsection 