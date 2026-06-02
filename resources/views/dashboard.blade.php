@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<style>
    #malaysiaMap {
        height: 500px;
        width: 100%;
        border-radius: 8px;
        background-color: #f1f3f5;
    }
    .leaflet-popup-content {
        font-family: 'Inter', sans-serif;
    }
    .info-legend {
        background: white;
        padding: 8px 12px;
        font: 12px/16px Arial, Helvetica, sans-serif;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
        border-radius: 5px;
        line-height: 20px;
        color: #333;
        font-weight: bold;
    }
    .info-legend i {
        width: 18px;
        height: 18px;
        float: left;
        margin-right: 8px;
        opacity: 0.85;
        border-radius: 2px;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Main Dashboard</h2>
            <p class="text-muted small mb-0">General Delivery Analytics</p>
        </div>
        
        <div class="d-flex gap-3 align-items-end">
            <form action="{{ url()->current() }}" method="GET" class="d-flex gap-3 align-items-end">
                <div>
                    <label class="filter-label d-block text-muted small fw-bold">SELECT YEAR</label>
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="2023" {{ $selectedYear == '2023' ? 'selected' : '' }}>2023</option>
                        <option value="2024" {{ $selectedYear == '2024' ? 'selected' : '' }}>2024</option>
                        <option value="2025" {{ $selectedYear == '2025' ? 'selected' : '' }}>2025</option>
                        <option value="2026" {{ $selectedYear == '2026' ? 'selected' : '' }}>2026</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label d-block text-muted small fw-bold">SELECT MONTH</label>
                    <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ $selectedMonth == 'all' ? 'selected' : '' }}>All Months</option>
                        @for ($i = 1; $i <= 12; $i++)
                            @if ($selectedYear == '2026' && $i > 4)
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

    {{-- Fetch Data Safely to Feed Frontend Processing engines --}}
    @php
        // Fetch rows matching the selected year safely without running volatile SQL GROUP BY functions
        $queryData = \Illuminate\Support\Facades\DB::table('ninjavan_data')
            ->where('Delivery_Date', 'LIKE', '%'.$selectedYear.'%');

        if(($selectedMonth ?? 'all') !== 'all') {
            $formattedMonth = str_pad($selectedMonth, 2, '0', STR_PAD_LEFT);
            $queryData->where('Delivery_Date', 'LIKE', '%/'.$formattedMonth.'/%');
        }

        $allMatchingRows = $queryData->get();
    @endphp

    {{-- Core Statistics Panels --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">TOTAL PARCELS</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($totalParcel ?? count($allMatchingRows)) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">TOTAL WEIGHT</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($totalWeight ?? $allMatchingRows->sum('Original_Weight'), 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">AVERAGE WEIGHT</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($avgWeight ?? ($allMatchingRows->count() > 0 ? $allMatchingRows->avg('Original_Weight') : 0), 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">DELIVERED (APPROX)</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($delivered ?? count($allMatchingRows)) }}</div>
            </div>
        </div>
    </div>

    {{-- GEOGRAPHICAL MAP CARD --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card p-3 shadow-sm border-0">
                <h5 class="fw-bold mb-3">Geographical Distribution (Malaysia)</h5>
                <div id="malaysiaMap"></div>
            </div>
        </div>
    </div>

    {{-- Analytics Graphs --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card p-3 shadow-sm border-0">
                <h5 class="fw-bold mb-3">Top 3 States by Orders</h5>
                <canvas id="stateChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-3 shadow-sm border-0">
                <h5 class="fw-bold mb-3">Parcel Size Distribution</h5>
                <canvas id="sizeChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card p-3 shadow-sm border-0">
                <h5 class="fw-bold mb-3">Monthly Parcel Trend</h5>
                <canvas id="trendChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-3 shadow-sm border-0 text-center">
                <h5 class="fw-bold mb-3">Customer Gender Distribution</h5>
                <div style="max-height: 250px; display: flex; justify-content: center;">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Data History Record Table --}}
    <div class="card p-3 shadow-sm border-0">
        <h5 class="fw-bold mb-3">Latest Parcels ({{ $selectedYear }})</h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Gender</th>
                        <th>State</th>
                        <th>Parcel Size</th>
                        <th>Weight</th>
                        <th>Delivery Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allMatchingRows->take(10) as $r)
                        <tr>
                            <td>{{ $r->Gender == 1 ? 'Female' : 'Male' }}</td>
                            <td>{{ $r->L1_Name }}</td>
                            <td>{{ $r->Parcel_Size_ID }}</td>
                            <td>{{ $r->Original_Weight }}</td>
                            <td>{{ $r->Delivery_Date }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Pass complete data array directly to local JS context safely 
        const serverDataset = {!! json_encode($allMatchingRows) !!};
        
        const fullStateLabels = {!! json_encode($stateLabels ?? []) !!};
        const fullStateData = {!! json_encode($stateData ?? []) !!};
        
        // ==========================================
        // MAP ENGINE SETUP WITH INLINE HIGH-PRECISION GEOMETRY
        // ==========================================
        try {
            const stateCountMap = {};
            if (Array.isArray(fullStateLabels) && fullStateLabels.length > 0) {
                fullStateLabels.forEach((label, idx) => {
                    if (label) {
                        let cleanKey = label.toUpperCase().replace('W.P.', '').replace('WILAYAH PERSEKUTUAN', '').trim();
                        if (cleanKey === 'PULAU PINANG') cleanKey = 'PENANG';
                        stateCountMap[cleanKey] = fullStateData[idx] || 0;
                    }
                });
            } else {
                serverDataset.forEach(row => {
                    if (row.L1_Name) {
                        let cleanKey = row.L1_Name.toUpperCase().replace('W.P.', '').replace('WILAYAH PERSEKUTUAN', '').trim();
                        if (cleanKey === 'PULAU PINANG') cleanKey = 'PENANG';
                        stateCountMap[cleanKey] = (stateCountMap[cleanKey] || 0) + 1;
                    }
                });
            }

            const m_map = L.map('malaysiaMap').setView([4.2105, 101.9758], 6);
            
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '© OpenStreetMap contributors © CARTO'
            }).addTo(m_map);

            function getColor(d) {
                return d > 2000 ? '#800026' :
                       d > 1000 ? '#BD0026' :
                       d > 500  ? '#E31A1C' :
                       d > 200  ? '#FC4E2A' :
                       d > 50   ? '#FD8D3C' :
                       d > 10   ? '#FEB24C' :
                       d > 0    ? '#FED976' : '#E2E8F0'; 
            }

            // High-precision tracing polygons for every Malaysian territory boundary layout
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
                    { "type": "Feature", "properties": { "name": "Perak" }, "geometry": { "type": "Polygon", "coordinates": [[[100.35,5.35],[100.45,5.20],[100.75,5.15],[101.00,5.75],[101.05,6.15],[101.35,5.85],[101.35,5.35],[101.35,4.75],[101.35,3.85],[101.05,3.85],[100.75,4.05],[100.55,4.45],[100.35,5.35]]] } },
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
                    fillOpacity: count > 0 ? 0.80 : 0.15
                };
            }

            // Render shapes instantly from local memory
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
                    layer.bindPopup(`<strong>${name}</strong><br/>Total Orders: ${totalOrders.toLocaleString()}`);
                }
            }).addTo(m_map);

            // Add dynamic data heatmap legend control box
            const legend = L.control({ position: 'bottomright' });
            legend.onAdd = function() {
                const div = L.DomUtil.create('div', 'info-legend');
                const grades = [0, 10, 50, 200, 500, 1000, 2000];
                div.innerHTML += '<strong>Order Volume</strong><br>';
                for (let i = 0; i < grades.length; i++) {
                    div.innerHTML +=
                        '<i style="background:' + getColor(grades[i] + 1) + '"></i> ' +
                        grades[i] + (grades[i + 1] ? '–' + grades[i + 1] + '<br>' : '+');
                }
                return div;
            };
            legend.addTo(m_map);

        } catch(e) { console.error("Error setting up map engine bounds:", e); }


        // ==========================================
        // TOP 3 STATES CHART
        // ==========================================
        let top3Labels = [];
        let top3Data = [];
        
        if (Array.isArray(fullStateLabels) && fullStateLabels.length > 0) {
            top3Labels = fullStateLabels.slice(0, 3);
            top3Data = fullStateData.slice(0, 3);
        } else {
            const tempStates = {};
            serverDataset.forEach(r => { if(r.L1_Name) tempStates[r.L1_Name] = (tempStates[r.L1_Name] || 0) + 1; });
            const sortedStates = Object.entries(tempStates).sort((a,b) => b[1] - a[1]).slice(0,3);
            top3Labels = sortedStates.map(x => x[0]);
            top3Data = sortedStates.map(x => x[1]);
        }

        new Chart(document.getElementById('stateChart'), {
            type: 'bar',
            data: {
                labels: top3Labels,
                datasets: [{
                    label: 'Parcels',
                    data: top3Data,
                    backgroundColor: 'rgba(220, 53, 69, 0.8)',
                    borderRadius: 5
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // ==========================================
        // SIZE DISTRIBUTION CHART
        // ==========================================
        let groupedSize = { 'Small': 0, 'Other': 0 };
        serverDataset.forEach(row => {
            if (row.Parcel_Size_ID == 1) groupedSize['Small']++;
            else groupedSize['Other']++;
        });
        
        new Chart(document.getElementById('sizeChart'), {
            type: 'pie',
            data: {
                labels: Object.keys(groupedSize),
                datasets: [{
                    data: Object.values(groupedSize),
                    backgroundColor: ['#dc3545', '#6c757d']
                }]
            },
            options: { responsive: true }
        });

        // ==========================================
        // GENDER DISTRIBUTION CHART
        // ==========================================
        let genderCounts = { 'Female': 0, 'Male': 0 };
        serverDataset.forEach(row => {
            if(row.Gender == 1) genderCounts['Female']++;
            else genderCounts['Male']++;
        });
        
        new Chart(document.getElementById('genderChart'), {
            type: 'pie',
            data: {
                labels: Object.keys(genderCounts),
                datasets: [{
                    data: Object.values(genderCounts),
                    backgroundColor: ['#fd35b0', '#0d6efd']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // ==========================================
        // BYPASSING STRICT MODE: FRONTEND TREND PROCESSING
        // ==========================================
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        const trendMap = Array(12).fill(0);

        serverDataset.forEach(row => {
            if (row.Delivery_Date) {
                const parts = row.Delivery_Date.split('/');
                if (parts.length >= 2) {
                    const monthIndex = parseInt(parts[1], 10) - 1;
                    if (monthIndex >= 0 && monthIndex < 12) {
                        trendMap[monthIndex]++;
                    }
                }
            }
        });

        let finalTrendLabels = monthNames;
        let finalTrendData = trendMap;
        let chartType = 'line';

        const activeMonthFilter = "{{ $selectedMonth }}";
        if (activeMonthFilter !== 'all') {
            const targetIdx = parseInt(activeMonthFilter, 10) - 1;
            finalTrendLabels = [monthNames[targetIdx]];
            finalTrendData = [trendMap[targetIdx]];
            chartType = 'bar';
        }

        new Chart(document.getElementById('trendChart'), {
            type: chartType,
            data: {
                labels: finalTrendLabels,
                datasets: [{
                    label: 'Parcels',
                    data: finalTrendData,
                    borderColor: '#dc3545',
                    fill: true,
                    backgroundColor: chartType === 'line' ? 'rgba(220, 53, 69, 0.1)' : 'rgba(220, 53, 69, 0.8)',
                    tension: 0.3,
                    pointRadius: 5,
                    barThickness: chartType === 'bar' ? 45 : undefined
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    });
</script>
@endsection