@extends('layouts.app')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    :root {
        --dash-bg: #1e1e24;
        --card-bg: rgba(255, 255, 255, 0.92);
        --primary-dark: #1e1e24;
        --secondary-slate: #2d3142;
        --accent-red: #c92c36;
        --text-main: #2d3142;
        --text-muted: #70798c;
        --border-color: rgba(218, 223, 230, 0.7);
        --radius-lg: 16px;
        --radius-md: 12px;
        --font-sans: 'Plus Jakarta Sans', sans-serif;
    }

    body {
        font-family: var(--font-sans);
        color: var(--text-main);
        background-color: var(--dash-bg);
        background-image: 
            linear-gradient(rgba(30, 30, 36, 0.80), rgba(30, 30, 36, 0.86)), 
            url("{{ asset('images/ninjavan-bg.png') }}");
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }

    .fw-extrabold { font-weight: 800; }
    
    .beauty-card {
        background: var(--card-bg);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.7);
        border-radius: var(--radius-lg);
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.1), 0 4px 12px -2px rgba(15, 23, 42, 0.05);
        transition: transform 0.22s ease, box-shadow 0.22s ease;
    }
    
    .beauty-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 32px -8px rgba(15, 23, 42, 0.18);
    }

    .glass-dark-card {
        background: linear-gradient(135deg, #1e1e24 0%, #2d3142 100%);
        border-radius: var(--radius-lg);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 12px 25px -5px rgba(30, 30, 36, 0.4);
        position: relative;
        overflow: hidden;
    }

    .glass-dark-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(201, 44, 54, 0.2) 0%, rgba(255, 255, 255, 0) 75%);
        pointer-events: none;
    }

    .premium-select {
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        padding: 0.5rem 2.5rem 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--secondary-slate);
        background-color: rgba(255, 255, 255, 0.95);
        cursor: pointer;
    }
    .premium-select:focus {
        border-color: var(--accent-red);
        box-shadow: 0 0 0 3px rgba(201, 44, 54, 0.15);
    }

    .stat-pill {
        border-left: 5px solid var(--secondary-slate);
        padding-left: 1rem;
    }
    .stat-pill.red-variant {
        border-left-color: var(--accent-red);
    }

    #malaysiaMap {
        height: 520px;
        width: 100%;
        border-radius: var(--radius-md);
        background-color: #f8fafc;
        border: 1px solid var(--border-color);
    }
    
    .info-legend {
        background: rgba(255, 255, 255, 0.95);
        padding: 12px 16px;
        font-family: var(--font-sans);
        font-size: 11px;
        font-weight: 600;
        box-shadow: 0 8px 16px -3px rgba(0,0,0,0.05);
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        color: var(--primary-dark);
        line-height: 22px;
    }
    .info-legend i {
        width: 16px;
        height: 16px;
        float: left;
        margin-right: 10px;
        margin-top: 3px;
        border-radius: 4px;
    }

    .metric-bubble {
        background-color: rgba(248, 250, 252, 0.8);
        border: 1px solid rgba(230, 235, 242, 0.9);
        border-radius: var(--radius-md);
        padding: 12px 16px;
    }
    .font-monospace-premium {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.85rem;
        letter-spacing: -0.02em;
    }

    .chart-container {
        position: relative;
        height: 260px;
        width: 100%;
    }
</style>

<div class="container-fluid py-4 px-4">
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-6">
            <span class="badge bg-danger bg-opacity-15 text-danger px-3 py-1.5 rounded-pill fw-bold text-uppercase tracking-wider mb-2" style="font-size: 0.75rem; letter-spacing: 0.05em;">LIVE OPERATIONS INTERFACE</span>
            <h1 class="fw-extrabold text-white tracking-tight mb-1" style="font-size: 1.85rem;">Main Dashboard</h1>
            <p class="text-white-50 small mb-0">Operational intelligence & localized routing metrics</p>
        </div>
        
        <div class="col-md-6 d-flex justify-content-md-end align-items-center">
            <form action="{{ url()->current() }}" method="GET" class="d-flex gap-3 bg-white bg-opacity-95 p-2 rounded-3 border shadow-sm">
                <div>
                    <select name="year" class="form-select premium-select" onchange="this.form.submit()">
                        <option value="2023" {{ $selectedYear == '2023' ? 'selected' : '' }}>2023 Operations</option>
                        <option value="2024" {{ $selectedYear == '2024' ? 'selected' : '' }}>2024 Operations</option>
                        <option value="2025" {{ $selectedYear == '2025' ? 'selected' : '' }}>2025 Operations</option>
                        <option value="2026" {{ $selectedYear == '2026' ? 'selected' : '' }}>2026 Operations</option>
                    </select>
                </div>
                <div>
                    <select name="month" class="form-select premium-select" onchange="this.form.submit()">
                        <option value="all" {{ $selectedMonth == 'all' ? 'selected' : '' }}>All Time-frames</option>
                        @for ($i = 1; $i <= 12; $i++)
                            @if (trim($selectedYear) === '2026' && $i > 4)
                                @break
                            @endif
                            <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                                {{ date("F", mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>

    @php
        $queryData = \Illuminate\Support\Facades\DB::table('ninjavan_data');

        if (($selectedMonth ?? 'all') !== 'all') {
            $paddedMonth = str_pad($selectedMonth, 2, '0', STR_PAD_LEFT);
            $rawMonth = (int)$selectedMonth;

            $queryData->where(function($q) use ($paddedMonth, $rawMonth, $selectedYear) {
                $q->where('Delivery_Date', 'LIKE', '%/' . $paddedMonth . '/' . $selectedYear . '%')
                  ->orWhere('Delivery_Date', 'LIKE', '%/' . $rawMonth . '/' . $selectedYear . '%')
                  ->orWhere('Delivery_Date', 'LIKE', $selectedYear . '-' . $paddedMonth . '-%');
            });
        } else {
            $queryData->where(function($q) use ($selectedYear) {
                $q->where('Delivery_Date', 'LIKE', '%/' . $selectedYear . '%')
                  ->orWhere('Delivery_Date', 'LIKE', $selectedYear . '-%');
            });
        }

        $allMatchingRows = $queryData->get();
        $avgTrust = \Illuminate\Support\Facades\DB::table('customer_ratings')->avg('rating_trust') ?? 0;
        $totalRowsCount = count($allMatchingRows);
        $totalFreightWeight = $allMatchingRows->sum('Original_Weight');
    @endphp

    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-dark-card p-4 d-flex flex-wrap justify-content-between align-items-center gap-3" style="background-color: #252836 !important; border-radius: 12px;">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white bg-opacity-10 rounded-3 p-2.5 text-warning fs-3 shadow-inner">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <div class="text-white opacity-75 small fw-bold text-uppercase tracking-wider" style="font-size: 0.75rem; letter-spacing: 0.04em;">Operational Integrity Index</div>
                        <div class="d-flex align-items-baseline gap-2 mt-1">
                            <h2 class="fw-extrabold text-white mb-0" style="font-size: 2rem; line-height: 1;">{{ number_format($avgTrust, 1) }}</h2>
                            <span class="text-white opacity-50 fw-semibold" style="font-size: 1rem;">/ 5.0 Global Rating</span>
                        </div>
                    </div>
                </div>
                <div class="fs-4 text-warning tracking-widest bg-white bg-opacity-10 px-3 py-1.5 rounded-pill shadow-sm d-flex gap-1 align-items-center">
                    @for($i = 1; $i <= 5; $i++)
                        {!! $i <= round($avgTrust) ? '★' : '<span class="text-white opacity-25">☆</span>' !!}
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card beauty-card p-4 stat-pill">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase tracking-wider">Cumulative Distribution Volume</div>
                        <div class="h2 fw-extrabold text-dark mt-1 mb-0" style="font-size: 2rem;">{{ number_format($totalRowsCount) }}</div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; min-width: 60px; max-width: 60px; min-height: 60px; max-height: 60px; background-color: #2d3142 !important;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="#f8fafc" viewBox="0 0 16 16" style="display: block;">
                            <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5zm.565 4.83 5.75-2.3a.5.5 0 0 1 .648.472v8.5a.5.5 0 0 1-.314.464l-6 2.332a.5.5 0 0 1-.37 0l-6-2.332A.5.5 0 0 1 1 12.5v-8.5a.5.5 0 0 1 .648-.472l5.75 2.3zm-7.251 1.76v3.96l4.5 1.748V7.705L1.5 5.962z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card beauty-card p-4 stat-pill red-variant">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase tracking-wider">Gross Freight Weight</div>
                        <div class="h2 fw-extrabold text-dark mt-1 mb-0" style="font-size: 2rem;">{{ number_format($totalFreightWeight, 2) }} <span class="fs-5 text-muted fw-normal">kg</span></div>
                    </div>
                    <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; min-width: 60px; max-width: 60px; min-height: 60px; max-height: 60px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="text-danger text-opacity-20" viewBox="0 0 16 16" style="display: block;">
                            <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 .17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.737 11H11v-1H1.294zM12 7a1 1 0 0 0-1 1v1h2.5l-1.642-2.053A1 1 0 0 0 12 7m1.75 3a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card beauty-card p-4 h-100">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-map text-danger fs-5"></i>
                    <h5 class="fw-bold text-dark m-0">Regional Density Matrix</h5>
                </div>
                <div id="malaysiaMap"></div>
            </div>
        </div>
        
        <div class="col-lg-5">
            <div class="card beauty-card p-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-light pb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-terminal-dash text-muted"></i>
                            <h5 class="fw-bold text-dark m-0" id="selected-state-title">State Inspector</h5>
                        </div>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1.5 rounded-pill small fw-bold text-uppercase" id="selected-state-badge">Idle Listening</span>
                    </div>

                    <div id="state-details-placeholder" class="text-center py-5">
                        <div class="p-3 bg-light rounded-circle d-inline-block mb-3">
                            <i class="bi bi-cursor-fill text-muted fs-3"></i>
                        </div>
                        <p class="text-dark fw-semibold small mb-1">No Region Selected</p>
                        <p class="text-muted small px-4">Click any colored polygon node on the map canvas to extract live timestamp data logs and real-time package counts.</p>
                    </div>

                    <div id="state-details-metrics" class="d-none">
                        <div class="mb-3">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Initial Logging Timestamp (Create_Time)</label>
                            <div class="metric-bubble font-monospace-premium border-start border-secondary border-3 text-dark fw-semibold" id="metric-create-time">--</div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="metric-bubble text-center">
                                    <label class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.02em;">Time taken to deliver order</label>
                                    <div class="text-dark"><span class="h3 fw-extrabold" id="metric-delivery-days">--</span> <span class="small text-muted fw-semibold">Days</span></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric-bubble text-center border-start border-danger border-2">
                                    <label class="text-muted d-block mb-1 text-uppercase fw-bold" style="font-size: 0.68rem; letter-spacing: 0.02em;">Regional Cargo</label>
                                    <div class="text-danger"><span class="h3 fw-extrabold" id="metric-total-parcels">--</span> <span class="small text-danger text-opacity-70 fw-semibold">pcs</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem;">Final Confirmed Delivery Date</label>
                            <div class="metric-bubble font-monospace-premium border-start border-success border-3 text-dark fw-semibold" id="metric-delivery-date">--</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card beauty-card p-4">
                <h6 class="fw-bold text-dark mb-3 text-uppercase tracking-wider small text-muted">Demographics Ratio</h6>
                <div class="chart-container">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card beauty-card p-4">
                <h6 class="fw-bold text-dark mb-3 text-uppercase tracking-wider small text-muted">Top Delivery Jurisdictions</h6>
                <div class="chart-container">
                    <canvas id="stateChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card beauty-card p-4">
                <h6 class="fw-bold text-dark mb-3 text-uppercase tracking-wider small text-muted">Monthly Flow Velocity</h6>
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    window.myGenderChartInstance = window.myGenderChartInstance || null;
    window.myStateChartInstance = window.myStateChartInstance || null;
    window.myTrendChartInstance = window.myTrendChartInstance || null;

    document.addEventListener('DOMContentLoaded', function() {
        
        const serverDataset = {!! json_encode($allMatchingRows ?? []) !!};
        
        // Helper function to robustly extract month from various date patterns (d/m/Y or Y-m-d)
        function getMonthFromRow(row) {
            const dStr = row.Delivery_Date || row.delivery_date;
            if (!dStr) return -1;
            
            let monthIndex = -1;
            if (String(dStr).includes('/')) {
                const parts = String(dStr).split('/');
                if (parts.length >= 2) monthIndex = parseInt(parts[1], 10) - 1;
            } else if (String(dStr).includes('-')) {
                const parts = String(dStr).split('-');
                if (parts.length >= 2) {
                    if(parts[0].length === 4) {
                        monthIndex = parseInt(parts[1], 10) - 1; // Y-m-d
                    } else {
                        monthIndex = parseInt(parts[1], 10) - 1; // d-m-Y
                    }
                }
            }
            return monthIndex;
        }

        // ==========================================
        // MAP ENGINE SETUP
        // ==========================================
        const stateCountMap = {};
        serverDataset.forEach(row => {
            const stateField = row.L1_Name || row.l1_name || row.State || row.state;
            if (stateField) {
                let cleanKey = stateField.toUpperCase().replace('W.P.', '').replace('WILAYAH PERSEKUTUAN', '').trim();
                if (cleanKey === 'PULAU PINANG') cleanKey = 'PENANG';
                stateCountMap[cleanKey] = (stateCountMap[cleanKey] || 0) + 1;
            }
        });

        try {
            const m_map = L.map('malaysiaMap').setView([4.2105, 101.9758], 6);
            
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '© CARTO'
            }).addTo(m_map);

            function getColor(d) {
                return d > 2000 ? '#4a0e12' :
                       d > 1000 ? '#6e1a24' :
                       d > 500  ? '#942431' :
                       d > 200  ? '#c92c36' :
                       d > 50   ? '#e06c75' :
                       d > 10   ? '#f0afb4' :
                       d > 0    ? '#fae1e3' : '#f8fafc'; 
            }

            const preciseMalaysiaGeoJSON = {
                "type": "FeatureCollection",
                "features": [
                    { "type": "Feature", "properties": { "name": "Johor" }, "geometry": { "type": "Polygon", "coordinates": [[[102.55,2.55],[102.75,2.45],[103.35,2.65],[103.95,2.45],[104.30,1.45],[104.10,1.35],[103.60,1.20],[103.40,1.35],[102.95,1.75],[102.55,2.05],[102.45,2.40],[102.55,2.55]]] } },
                    { "type": "Feature", "properties": { "name": "Kedah" }, "geometry": { "type": "Polygon", "coordinates": [[[100.35,6.40],[100.55,6.45],[101.05,6.15],[101.00,5.75],[100.75,5.15],[100.45,5.20],[100.35,5.35],[100.35,5.80],[100.20,6.15],[100.35,6.40]]] } },
                    { "type": "Feature", "properties": { "name": "Kelantan" }, "geometry": { "type": "Polygon", "coordinates": [[[101.35,5.85],[101.55,5.90],[101.75,5.90],[102.15,6.25],[102.55,6.20],[102.65,5.75],[102.45,4.75],[101.85,4.65],[101.35,4.75],[101.35,5.35],[101.35,5.85]]] } },
                    { "type": "Feature", "properties": { "name": "Melaka" }, "geometry": { "type": "Polygon", "coordinates": [[[102.05,2.40],[102.35,2.45],[102.55,2.35],[102.45,2.00],[102.15,2.10],[102.05,2.40]]] } },
                    { "type": "Feature", "properties": { "name": "Negeri Sembilan" }, "geometry": { "type": "Polygon", "coordinates": [[[101.80,3.15],[102.00,3.20],[102.35,3.05],[102.75,2.75],[102.55,2.35],[102.35,2.45],[102.05,2.40],[101.75,2.45],[101.80,3.15]]] } },
                    { "type": "Feature", "properties": { "name": "Pahang" }, "geometry": { "type": "Polygon", "coordinates": [[[101.35,4.75],[101.85,4.65],[102.45,4.75],[102.65,4.75],[102.75,4.25],[103.45,4.15],[103.50,3.55],[103.65,2.65],[103.35,2.65],[102.75,2.75],[102.35,3.05],[102.00,3.20],[101.90,3.55],[101.65,3.65],[101.35,3.85],[101.35,4.75]]] } },
                    { "type": "Feature", "properties": { "name": "Penang" }, "geometry": { "type": "Polygon", "coordinates": [[[100.15,5.55],[100.55,5.55],[100.55,5.15],[100.20,5.15],[100.15,5.35],[100.15,5.55]]] } },
                    { "type": "Feature", "properties": { "name": "Perak" }, "geometry": { "type": "Polygon", "coordinates": [[[100.35,5.35],[100.45,5.20],[100.75,5.15],[101.00,5.75],[101.05,6.15],[101.35,5.85],[100.35,5.35]]] } },
                    { "type": "Feature", "properties": { "name": "Perlis" }, "geometry": { "type": "Polygon", "coordinates": [[[100.10,6.70],[100.30,6.65],[100.35,6.40],[100.20,6.15],[100.05,6.45],[100.10,6.70]]] } },
                    { "type": "Feature", "properties": { "name": "Selangor" }, "geometry": { "type": "Polygon", "coordinates": [[[100.75,4.05],[101.05,3.85],[101.35,3.85],[101.65,3.65],[101.90,3.55],[102.00,3.20],[101.80,3.15],[101.75,2.45],[101.45,2.65],[101.25,2.95],[101.10,3.25],[100.75,4.05]]] } },
                    { "type": "Feature", "properties": { "name": "Terengganu" }, "geometry": { "type": "Polygon", "coordinates": [[[102.55,6.20],[102.75,5.85],[102.95,5.75],[103.15,5.45],[103.50,4.75],[103.45,4.15],[102.75,4.25],[102.65,4.75],[102.65,5.75],[102.55,6.20]]] } },
                    { "type": "Feature", "properties": { "name": "Kuala Lumpur" }, "geometry": { "type": "Polygon", "coordinates": [[[101.60,3.25],[101.75,3.25],[101.75,3.10],[101.60,3.10],[101.60,3.25]]] } },
                    { "type": "Feature", "properties": { "name": "Sabah" }, "geometry": { "type": "Polygon", "coordinates": [[[115.10,6.25],[115.60,6.85],[116.70,7.25],[117.50,6.75],[119.10,5.55],[119.30,4.65],[118.50,4.25],[117.50,4.15],[115.20,4.25],[115.15,4.85],[115.10,6.25]]] } },
                    { "type": "Feature", "properties": { "name": "Sarawak" }, "geometry": { "type": "Polygon", "coordinates": [[[109.50,2.05],[110.50,1.55],[111.20,2.55],[112.50,3.25],[113.80,4.45],[115.15,4.85],[115.20,4.25],[114.20,3.55],[113.10,2.15],[111.50,1.25],[109.50,2.05]]] } }
                ]
            };

            function styleFeature(feature) {
                let stateName = feature.properties.name ? feature.properties.name.toUpperCase() : '';
                stateName = stateName.replace('W.P.', '').replace('WILAYAH PERSEKUTUAN', '').trim();
                if (stateName === 'PULAU PINANG') stateName = 'PENANG';

                let count = stateCountMap[stateName] || 0;
                if (count === 0) {
                    for (let key in stateCountMap) {
                        if (stateName.includes(key) || key.includes(stateName)) {
                            count = stateCountMap[key];
                            break;
                        }
                    }
                }

                return {
                    fillColor: getColor(count),
                    weight: 1.5,
                    opacity: 1,
                    color: '#ffffff',
                    fillOpacity: count > 0 ? 0.85 : 0.3
                };
            }

            L.geoJson(preciseMalaysiaGeoJSON, {
                style: styleFeature,
                onEachFeature: function(feature, layer) {
                    const name = feature.properties.name || "Unknown State";
                    let checkName = name.toUpperCase().replace('W.P.', '').replace('WILAYAH PERSEKUTUAN', '').trim();
                    if (checkName === 'PULAU PINANG') checkName = 'PENANG';

                    let totalOrders = stateCountMap[checkName] || 0;
                    if (totalOrders === 0) {
                        for (let key in stateCountMap) {
                            if (checkName.includes(key) || key.includes(checkName)) {
                                totalOrders = stateCountMap[key];
                                break;
                            }
                        }
                    }
                    
                    layer.bindPopup(`<div style="font-family:'Plus Jakarta Sans',sans-serif; padding:2px;"><strong style="font-size:13px; color:#1e1e24;">${name}</strong><br/><span style="color:#70798c; font-size:11px;">Volume:</span> <strong style="color:#c92c36;">${totalOrders.toLocaleString()} orders</strong></div>`);

                    layer.on('click', function() {
                        document.getElementById('selected-state-title').innerText = name;
                        document.getElementById('selected-state-badge').innerText = 'INSPECTING STATE';
                        document.getElementById('selected-state-badge').className = 'badge bg-danger bg-opacity-15 text-danger px-3 py-1.5 rounded-pill small fw-bold text-uppercase';

                        const matchedRows = serverDataset.filter(row => {
                            const stateField = row.L1_Name || row.l1_name || row.State || row.state;
                            if (!stateField) return false;
                            let targetKey = stateField.toUpperCase().replace('W.P.', '').replace('WILAYAH PERSEKUTUAN', '').trim();
                            if (targetKey === 'PULAU PINANG') targetKey = 'PENANG';
                            return targetKey === checkName;
                        });

                        let createTime = 'N/A';
                        let deliveryDateStr = 'N/A';
                        let leadDays = '--';

                        if (matchedRows.length > 0) {
                            const latestRecord = matchedRows[matchedRows.length - 1];
                            createTime = latestRecord.Create_Time || latestRecord.create_time || 'N/A';
                            deliveryDateStr = latestRecord.Delivery_Date || latestRecord.delivery_date || 'N/A';
                            
                            if (createTime !== 'N/A' && deliveryDateStr !== 'N/A') {
                                try {
  // UNIVERSAL PARSER: Handles timestamps, slashes, hyphens, and missing padded zeros
const parseDateRobustly = (dateString) => {
    if (!dateString || dateString === 'N/A') return new Date(NaN);
    
    // Remove any time element and whitespace
    let cleanStr = String(dateString).trim().split(' ')[0];
    let segments = [];
    
    if (cleanStr.includes('/')) {
        segments = cleanStr.split('/');
    } else if (cleanStr.includes('-')) {
        segments = cleanStr.split('-');
    }

    if (segments.length === 3) {
        let year, month, day;

        if (segments[0].length === 4) {
            // Format: YYYY-MM-DD or YYYY-M-D
            year = parseInt(segments[0], 10);
            month = parseInt(segments[1], 10) - 1;
            day = parseInt(segments[2], 10);
            
            // Auto-heal truncated trailing zero (e.g., '3' -> '30') if it results in an invalid day for that month
            if (day < 10 && segments[2].length === 1) {
                let testObj = new Date(year, month, day * 10);
                if (testObj.getMonth() === month) {
                    day = day * 10;
                }
            }
        } else {
            // Format: DD/MM/YYYY or MM/DD/YYYY
            // Check if first segment is clearly the day
            if (parseInt(segments[0], 10) > 12) {
                day = parseInt(segments[0], 10);
                month = parseInt(segments[1], 10) - 1;
            } else {
                // Default to MM/DD/YYYY assuming standard export rules
                month = parseInt(segments[0], 10) - 1;
                day = parseInt(segments[1], 10);
            }
            year = parseInt(segments[2], 10);
        }

        return new Date(year, month, day);
    }
    
    return new Date(dateString);
};

let createdDateObj = parseDateRobustly(createTime);
let deliveredDateObj = parseDateRobustly(deliveryDateStr);

if (!isNaN(createdDateObj.getTime()) && !isNaN(deliveredDateObj.getTime())) {
    
    // HEALING ENGINE: Only switch to day/month swap if the dates are still inverted after initial padding checks
    if (deliveredDateObj < createdDateObj) {
        let cleanDelivStr = String(deliveryDateStr).trim().split(' ')[0];
        let delivSegs = cleanDelivStr.includes('/') ? cleanDelivStr.split('/') : cleanDelivStr.split('-');
        
        if (delivSegs.length === 3) {
            let healedDeliveryObj;
            if (delivSegs[0].length === 4) {
                // YYYY-DD-MM conversion attempt
                healedDeliveryObj = new Date(parseInt(delivSegs[0], 10), parseInt(delivSegs[2], 10) - 1, parseInt(delivSegs[1], 10));
            } else {
                // MM/DD/YYYY to DD/MM/YYYY swap attempt
                healedDeliveryObj = new Date(parseInt(delivSegs[2], 10), parseInt(delivSegs[0], 10) - 1, parseInt(delivSegs[1], 10));
            }

            if (healedDeliveryObj >= createdDateObj && !isNaN(healedDeliveryObj.getTime())) {
                deliveredDateObj = healedDeliveryObj;
            }
        }
    }

    // Final Math Calculation
    if (deliveredDateObj >= createdDateObj) {
        const diffTime = Math.abs(deliveredDateObj - createdDateObj);
        leadDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    } else {
        // If it's still inverted, compute absolute difference instead of hardcoding '0'
        const diffTime = Math.abs(deliveredDateObj - createdDateObj);
        leadDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    }
}

if(isNaN(leadDays) || leadDays > 90 || leadDays < 0) leadDays = '0';
                                    
                                } catch (err) { 
                                    console.error("Date processing error:", err);
                                    leadDays = '--'; 
                                }
                            }
                        }

                        document.getElementById('state-details-placeholder').classList.add('d-none');
                        document.getElementById('state-details-metrics').classList.remove('d-none');

                        document.getElementById('metric-create-time').innerText = createTime;
                        document.getElementById('metric-delivery-days').innerText = leadDays;
                        document.getElementById('metric-total-parcels').innerText = matchedRows.length.toLocaleString();
                        document.getElementById('metric-delivery-date').innerText = deliveryDateStr;
                    });
                }
            }).addTo(m_map);

            const legend = L.control({ position: 'bottomright' });
            legend.onAdd = function() {
                const div = L.DomUtil.create('div', 'info-legend');
                const grades = [0, 10, 50, 200, 500, 1000, 2000];
                div.innerHTML += '<div class="mb-1 text-muted text-uppercase" style="font-size:9px; letter-spacing:0.04em;">Parcel Load</div>';
                for (let i = 0; i < grades.length; i++) {
                    div.innerHTML +=
                        '<i style="background:' + getColor(grades[i] + 1) + '"></i> ' +
                        grades[i] + (grades[i + 1] ? '–' + grades[i + 1] + '<br>' : '+');
                }
                return div;
            };
            legend.addTo(m_map);

        } catch(e) { 
            console.error("Map initialization context failed: ", e); 
        }

        // ==========================================
        // CHART INTELLIGENCE
        // ==========================================
        let maleCount = 0, femaleCount = 0, companyCount = 0;
        serverDataset.forEach(row => {
            const rawGender = row.Gender !== undefined ? row.Gender : row.gender;
            if (rawGender !== undefined && rawGender !== null) {
                const g = String(rawGender).trim().toLowerCase();
                if (g === '1' || g === 'male' || g === 'm' || g === 'lelaki') maleCount++;
                else if (g === '2' || g === '0' || g === 'female' || g === 'f' || g === 'perempuan') femaleCount++;
                else companyCount++;
            } else companyCount++;
        });

        if (window.myGenderChartInstance !== null) window.myGenderChartInstance.destroy();
        window.myGenderChartInstance = new Chart(document.getElementById('genderChart'), {
            type: 'doughnut',
            data: {
                labels: ['Male Clients', 'Female Clients', 'Corporate'],
                datasets: [{
                    data: [maleCount, femaleCount, companyCount],
                    backgroundColor: ['#2d3142', '#c92c36', '#f59e0b'],
                    hoverOffset: 4,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { family: 'Plus Jakarta Sans', weight: 600 } } }
                }
            }
        });

        const tempStates = {};
        serverDataset.forEach(r => { 
            const stateField = r.L1_Name || r.l1_name || r.State || r.state;
            if(stateField) {
                let formatted = stateField.toUpperCase().replace('W.P.', '').replace('WILAYAH PERSEKUTUAN', '').trim();
                if (formatted === 'PULAU PINANG') formatted = 'PENANG';
                tempStates[formatted] = (tempStates[formatted] || 0) + 1;
            }
        });
        const sortedStates = Object.entries(tempStates).sort((a,b) => b[1] - a[1]).slice(0,3);
        let top3Labels = sortedStates.map(x => x[0]);
        let top3Data = sortedStates.map(x => x[1]);

        if (window.myStateChartInstance !== null) window.myStateChartInstance.destroy();
        window.myStateChartInstance = new Chart(document.getElementById('stateChart'), {
            type: 'bar',
            data: {
                labels: top3Labels,
                datasets: [{
                    label: 'Parcels Volume',
                    data: top3Data,
                    backgroundColor: 'rgba(45, 49, 62, 0.85)',
                    hoverBackgroundColor: '#2d3142',
                    borderRadius: 8
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Plus Jakarta Sans' } } },
                    x: { grid: { display: false }, ticks: { font: { family: 'Plus Jakarta Sans', weight: 600 } } }
                }
            }
        });

        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const trendMap = Array(12).fill(0);
        serverDataset.forEach(row => {
            let mIdx = getMonthFromRow(row);
            if (mIdx >= 0 && mIdx < 12) trendMap[mIdx]++;
        });

        let finalTrendLabels = monthNames;
        let finalTrendData = trendMap;
        const activeMonthFilter = "{{ $selectedMonth }}";
        if (activeMonthFilter !== 'all') {
            const targetIdx = parseInt(activeMonthFilter, 10) - 1;
            finalTrendLabels = [monthNames[targetIdx]];
            finalTrendData = [trendMap[targetIdx]];
        }

        if (window.myTrendChartInstance !== null) window.myTrendChartInstance.destroy();
        window.myTrendChartInstance = new Chart(document.getElementById('trendChart'), {
            type: 'bar',
            data: {
                labels: finalTrendLabels,
                datasets: [{
                    label: 'Parcels Flow',
                    data: finalTrendData,
                    backgroundColor: 'rgba(201, 44, 54, 0.85)',
                    hoverBackgroundColor: '#c92c36',
                    borderRadius: 6,
                    barPercentage: 0.7
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { grid: { color: '#f1f5f9' }, beginAtZero: true },
                    x: { grid: { display: false } }
                } 
            }
        });
    });
</script>
@endsection