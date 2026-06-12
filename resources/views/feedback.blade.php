@extends('layouts.app')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    :root {
        --text-main: #ffffff; 
        --text-muted: #cbd5e1; /* Made noticeably brighter for small subheadings */
        --border-color: rgba(255, 255, 255, 0.1);
        --radius-lg: 16px;
        --radius-md: 12px;
        --font-sans: 'Plus Jakarta Sans', sans-serif;
    }

    /* Uses your sunny rating-bg.png.png illustration as the layout background */
    .dashboard-bg-wrapper {
        position: relative;
        min-height: 100vh;
        padding: 2rem;
        background-color: #0f172a; 
        background-image: url('{{ asset("images/rating-bg.png.png") }}'); 
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }

    /* A sleek dark translucent mask to balance the bright sunny image with white dashboard text */
    .dashboard-bg-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.75) 0%, rgba(30, 41, 59, 0.7) 100%);
        z-index: 1;
    }

    /* Interactive layer positioned safely above background rules */
    .dashboard-relative-content {
        position: relative;
        z-index: 2;
        font-family: var(--font-sans);
    }

    /* Premium Frosted Glassmorphic Dark UI Containers */
    .glass-feedback-card {
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(20px) saturate(140%);
        -webkit-backdrop-filter: blur(20px) saturate(140%);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-lg);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
    }

    /* Header Tracking Pill Targets */
    .counter-badge-pill {
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(8px);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 0.6rem 1.2rem;
        text-align: center;
        min-width: 140px;
    }

    /* Top Tier Metrics Accent Highlights */
    .metric-top-strip {
        background: rgba(15, 23, 42, 0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--radius-lg);
        text-align: center;
        padding: 1.5rem 1rem;
        box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.3);
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .metric-top-strip:hover {
        transform: translateY(-4px);
    }
    .metric-top-strip.blue-accent { border-top: 4px solid #3b82f6; }
    .metric-top-strip.green-accent { border-top: 4px solid #22c55e; }
    .metric-top-strip.cyan-accent { border-top: 4px solid #06b6d4; }

    /* Fixes hidden text readability inside metrics cards and counters */
    .text-muted.small.fw-bold.tracking-wider,
    .text-muted.small.fw-bold.text-uppercase.tracking-wider {
        color: #e2e8f0 !important; /* Forces the small label text over numbers to a crisp near-white color */
        opacity: 0.9;
    }

    /* Fixed visibility for the subheader text under Customer Feedback */
    p.text-muted.small {
        color: #cbd5e1 !important; 
    }

    /* Forces /5 text labels next to big scores to show clearly */
    .h2 .text-muted, .text-end .text-muted {
        color: #94a3b8 !important;
    }

    /* Courier Status Label Badges */
    .courier-pill-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.3rem 0.75rem;
        border-radius: 6px;
        display: inline-block;
    }
    .courier-ninja, .courier-ninjavan { background-color: rgba(225, 29, 72, 0.2); color: #fb7185; border: 1px solid rgba(225, 29, 72, 0.3); }
    .courier-jt, .courier-j-t-express { background-color: rgba(220, 38, 38, 0.2); color: #f87171; border: 1px solid rgba(220, 38, 38, 0.3); }
    .courier-poslaju, .courier-pos-laju { background-color: rgba(234, 88, 12, 0.2); color: #fb923c; border: 1px solid rgba(234, 88, 12, 0.3); }
    .courier-fallback { background-color: rgba(255, 255, 255, 0.08); color: #cbd5e1; }

    /* Complete dark structural fixes to defeat bright light layout visibility bugs */
    .table {
        color: #ffffff !important;
        background: transparent !important;
    }

    .table td {
        color: #f1f5f9 !important; 
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        background: transparent !important;
    }

    .sticky-table-head th {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #94a3b8 !important;
        font-weight: 700;
        letter-spacing: 0.05em;
        background-color: rgba(15, 23, 42, 0.85) !important;
        border-bottom: 2px solid rgba(255, 255, 255, 0.15) !important;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }

    .table-responsive {
        background: transparent !important;
    }
</style>

<div class="dashboard-bg-wrapper">
    <div class="dashboard-bg-overlay"></div>

    <div class="container-fluid p-0 dashboard-relative-content">
        {{-- Header Layout --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h2 class="fw-extrabold text-white tracking-tight mb-1" style="font-size: 1.85rem;">Customer Feedback</h2>
                <p class="text-muted small mb-0 fw-medium">Service Quality & Sentiment Analysis</p>
            </div>
            <div class="d-flex gap-3">
                <div class="counter-badge-pill">
                    <div class="text-muted small fw-bold tracking-wider" style="font-size: 0.68rem; text-transform: uppercase;">Written Comments</div>
                    <div class="h4 fw-extrabold mb-0 text-info mt-0.5">{{ $feedback->count() }}</div>
                </div>
                <div class="counter-badge-pill">
                    <div class="text-muted small fw-bold tracking-wider" style="font-size: 0.68rem; text-transform: uppercase;">Survey Respondents</div>
                    <div class="h4 fw-extrabold mb-0 text-danger mt-0.5">{{ $totalResponses }}</div>
                </div>
            </div>
        </div>

        {{-- Balanced 3-Column Metric Overview Row --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card metric-top-strip blue-accent shadow-sm">
                    <div class="text-muted small fw-bold text-uppercase tracking-wider mb-2" style="font-size: 0.72rem;">Punctuality Score</div>
                    <div class="h2 fw-extrabold text-white mb-1">{{ number_format($avgPunctuality, 1) }}<span class="text-muted fw-normal" style="font-size: 1rem;">/5</span></div>
                    <div class="text-warning small mt-1">
                        @for($i=1; $i<=5; $i++)
                            <i class="bi bi-star{{ $i <= round($avgPunctuality) ? '-fill' : '' }} mx-0.5"></i>
                        @endfor
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card metric-top-strip green-accent shadow-sm">
                    <div class="text-muted small fw-bold text-uppercase tracking-wider mb-2" style="font-size: 0.72rem;">Parcel Condition</div>
                    <div class="h2 fw-extrabold text-white mb-1">{{ number_format($avgCondition, 1) }}<span class="text-muted fw-normal" style="font-size: 1rem;">/5</span></div>
                    <div class="text-warning small mt-1">
                        @for($i=1; $i<=5; $i++)
                            <i class="bi bi-star{{ $i <= round($avgCondition) ? '-fill' : '' }} mx-0.5"></i>
                        @endfor
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card metric-top-strip cyan-accent shadow-sm">
                    <div class="text-muted small fw-bold text-uppercase tracking-wider mb-2" style="font-size: 0.72rem;">Courier Attitude</div>
                    <div class="h2 fw-extrabold text-white mb-1">{{ number_format($avgAttitude, 1) }}<span class="text-muted fw-normal" style="font-size: 1rem;">/5</span></div>
                    <div class="text-warning small mt-1">
                        @for($i=1; $i<=5; $i++)
                            <i class="bi bi-star{{ $i <= round($avgAttitude) ? '-fill' : '' }} mx-0.5"></i>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Section: Trust Distribution Chart & Comments Table --}}
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card glass-feedback-card p-4 h-100">
                    <h5 class="fw-bold text-white mb-4" style="font-size: 1.05rem;">NinjaVan Trust Level</h5>
                    <div style="height: 300px; position: relative;">
                        <canvas id="trustChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card glass-feedback-card p-4 h-100">
                    <h5 class="fw-bold text-white mb-4" style="font-size: 1.05rem;">Recent Comments & Reasons</h5>
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto; border-radius: 8px;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="sticky-top ready sticky-table-head">
                                <tr>
                                    <th style="width: 30%;">Preferred Courier</th>
                                    <th style="width: 55%;">Reasoning</th>
                                    <th style="width: 15%;" class="text-end">Trust</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($feedback as $f)
                                @php
                                    $sanitizedClass = strtolower(str_replace([' ', '&', '.'], '-', trim($f->preferred_courier)));
                                @endphp
                                <tr>
                                    <td>
                                        <span class="courier-pill-badge courier-{{ $sanitizedClass }} courier-fallback">
                                            {{ $f->preferred_courier }}
                                        </span>
                                    </td>
                                    <td class="small text-white text-wrap" style="max-width: 320px; line-height: 1.4; font-weight: 500;">
                                        {{ $f->reason }}
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-white">{{ $f->trust_rating }}</span><span class="text-muted small">/5</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trustLabels = {!! json_encode($trustLabels) !!};
        const trustData = {!! json_encode($trustData) !!};

        new Chart(document.getElementById('trustChart'), {
            type: 'bar',
            data: {
                labels: trustLabels.map(l => 'Level ' + l),
                datasets: [{
                    label: 'Respondents',
                    data: trustData,
                    backgroundColor: '#e11d48', 
                    borderRadius: 6,
                    barPercentage: 0.45
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        ticks: { stepSize: 1, color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 10, weight: 600 } },
                        grid: { color: 'rgba(255, 255, 255, 0.06)' }
                    },
                    x: {
                        ticks: { color: '#94a3b8', font: { family: 'Plus Jakarta Sans', size: 11, weight: 600 } },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endsection