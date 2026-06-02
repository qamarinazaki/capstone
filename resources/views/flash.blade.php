@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-danger">⚡ Operational Flash Report</h2>
            <p class="text-muted small mb-0">High-frequency single-day operational vitals snapshot</p>
        </div>
        
        <div>
            <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2 align-items-end">
                <div>
                    <label class="text-muted small fw-bold d-block">CHOOSE OPERATIONAL DATE</label>
                    <input type="text" 
                           id="operational_flash_picker"
                           name="date" 
                           class="form-control form-control-sm text-center fw-bold text-dark bg-white" 
                           value="{{ (strpos($selectedDate, '/') !== false) ? date('Y-m-d', strtotime(str_replace('/', '-', $selectedDate))) : $selectedDate }}" 
                           readonly
                           style="max-width: 170px; letter-spacing: 0.5px; cursor: pointer;">
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0 bg-dark text-white">
                <div class="text-light small fw-bold text-uppercase">Flash Volume (Parcels)</div>
                <div class="display-5 fw-bold text-warning my-1">{{ number_format($totalParcels) }}</div>
                <div class="small text-muted">Processed on target date</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0 bg-white">
                <div class="text-muted small fw-bold text-uppercase">Total Cargo Mass</div>
                <div class="display-5 fw-bold text-dark my-1">{{ number_format($totalWeight, 2) }} <span class="fs-6 text-muted">kg</span></div>
                <div class="small text-success">▲ Physical payload movement</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 shadow-sm border-0 bg-white">
                <div class="text-muted small fw-bold text-uppercase">Average Density Profile</div>
                <div class="display-5 fw-bold text-dark my-1">{{ number_format($avgWeight, 2) }} <span class="fs-6 text-muted">kg/pc</span></div>
                <div class="small text-muted">Mean payload structural mass</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card p-3 shadow-sm border-0 h-100">
                <h5 class="fw-bold mb-3 text-dark">Distribution Concentration by Destination</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Regional Destination</th>
                                <th></th> <th class="text-end">Load Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(array_slice($stateBreakdown, 0, 6, true) as $state => $count)
                                <tr>
                                    <td class="fw-bold text-secondary">{{ $state }}</td>
                                    <td>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $totalParcels > 0 ? ($count / $totalParcels) * 100 : 0 }}%"></div>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-dark">{{ number_format($count) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No distribution data recorded for this timestamp.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card p-3 shadow-sm border-0 h-100">
                <h5 class="fw-bold mb-3 text-dark">Immediate Package Size Profile Mix</h5>
                <div class="d-flex align-items-center justify-content-center h-100 py-3">
                    <div class="w-100" style="max-height: 250px;">
                        <canvas id="flashSizeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Initialize Strict Year Boundary Calendar 
        flatpickr("#operational_flash_picker", {
            dateFormat: "Y-m-d",
            minDate: "2023-01-01",
            maxDate: "2026-12-31",
            disableMobile: "true", // Forces consistent theme layout engine styling across mobile viewports
            onChange: function(selectedDates, dateStr, instance) {
                instance.element.form.submit();
            }
        });

        // 2. Initialize Operational Size Profile Mix Chart Engine
        const sizeCtx = document.getElementById('flashSizeChart');
        if(sizeCtx) {
            new Chart(sizeCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Small Package (ID:1)', 'Other Multi-Sizes'],
                    datasets: [{
                        data: [{{ (int)$sizes['Small'] }}, {{ (int)$sizes['Other'] }}],
                        backgroundColor: ['#dc3545', '#212529'],
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { weight: 'bold' } }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush