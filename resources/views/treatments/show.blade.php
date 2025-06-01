@extends('layouts.app')

@section('title', 'Treatment Details')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-spa me-2"></i>Treatment Details</span>
        <div>
            <a href="{{ route('treatments.edit', $treatment) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('treatments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table">
                    <tr>
                        <th style="width: 30%;">ID</th>
                        <td>{{ $treatment->id }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>{{ $treatment->name }}</td>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <td>Rp {{ number_format($treatment->price, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <td>
                            <span class="badge bg-{{ $treatment->is_bundle ? 'info' : 'primary' }}">
                                {{ $treatment->is_bundle ? 'Bundle' : 'Individual' }}
                            </span>
                        </td>
                    </tr>
                    @if($treatment->is_bundle)
                    <tr>
                        <th>Bundle Name</th>
                        <td>{{ $treatment->bundle_name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Created At</th>
                        <td>{{ $treatment->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Updated At</th>
                        <td>{{ $treatment->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Description</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $treatment->description ?? 'No description available' }}</p>
                    </div>
                </div>
                
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Treatment Usage</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-bar">
                            <canvas id="treatmentUsageChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            Number of times this treatment has been ordered this year
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script>
    // Treatment Usage Chart
    var ctxUsage = document.getElementById("treatmentUsageChart");
    if (ctxUsage) {
        var treatmentUsageChart = new Chart(ctxUsage, {
            type: 'bar',
            data: {
                labels: @json($usageMonthNames),
                datasets: [{
                    label: "Times Ordered",
                    backgroundColor: "#4e73df",
                    hoverBackgroundColor: "#2e59d9",
                    borderColor: "#4e73df",
                    data: @json($usageValues),
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'month'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 12
                        },
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) {
                                if (Math.floor(value) === value) {
                                    return value;
                                }
                            },
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: false
                },
                tooltips: {
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': ' + tooltipItem.yLabel;
                        }
                    }
                },
            }
        });
    }
</script>
@endsection 