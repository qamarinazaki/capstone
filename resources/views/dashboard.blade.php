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
        padding: 6px 10px;
        font: 12px/14px Arial, Helvetica, sans-serif;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
        border-radius: 5px;
        line-height: 18px;
        color: #555;
    }
    .info-legend i {
        width: 18px;
        height: 18px;
        float: left;
        margin-right: 8px;
        opacity: 0.7;
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

    {{-- Core Statistics Panels --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">TOTAL PARCELS</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($totalParcel ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">TOTAL WEIGHT</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($totalWeight ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">AVERAGE WEIGHT</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($avgWeight ?? 0, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">DELIVERED (APPROX)</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($delivered ?? 0) }}</div>
            </div>
        </div>
    </div>

    {{-- QAMARINA'S GEOGRAPHICAL MAP CARD --}}
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
                    @php
                        $latest = \Illuminate\Support\Facades\DB::table('ninjavan_data')
                            ->where('Delivery_Date', 'LIKE', '%'.$selectedYear.'%');
                        
                        if(($selectedMonth ?? 'all') !== 'all') {
                            $formattedMonth = str_pad($selectedMonth, 2, '0', STR_PAD_LEFT);
                            $latest->where('Delivery_Date', 'LIKE', '%/'.$formattedMonth.'/%');
                        }

                        $rows = $latest->orderByDesc('Delivery_Date')->limit(10)->get();
                    @endphp
                    @foreach($rows as $r)
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
        
        // ==========================================
        // SHARED BACKEND PAYLOADS (DECLARED ONCE ONLY)
        // ==========================================
        const fullStateLabels = {!! json_encode($stateLabels ?? []) !!};
        const fullStateData = {!! json_encode($stateData ?? []) !!};
        
        // ==========================================
        // 0. QAMARINA'S CHOROPLETH GEODATA MAP LOGIC
        // ==========================================
        try {
            const stateCountMap = {};
            if (Array.isArray(fullStateLabels)) {
                fullStateLabels.forEach((label, idx) => {
                    if (label) {
                        const cleanKey = label.toUpperCase()
                                              .replace('W.P.', '')
                                              .replace('WILAYAH PERSEKUTUAN', '')
                                              .trim();
                        stateCountMap[cleanKey] = fullStateData[idx] || 0;
                    }
                });
            }

            const m_map = L.map('malaysiaMap').setView([4.2105, 101.9758], 6);
            
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '© OpenStreetMap contributors © CARTO'
            }).addTo(m_map);

            function getColor(d) {
                return d > 1000 ? '#800026' :
                       d > 500  ? '#BD0026' :
                       d > 200  ? '#E31A1C' :
                       d > 100  ? '#FC4E2A' :
                       d > 50   ? '#FD8D3C' :
                       d > 20   ? '#FEB24C' :
                       d > 10   ? '#FED976' : '#FFEDA0';
            }

            function styleFeature(feature) {
                let stateName = feature.properties.name ? feature.properties.name.toUpperCase() : '';
                stateName = stateName.replace('W.P.', '').replace('WILAYAH PERSEKUTUAN', '').trim();
                
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
                    fillOpacity: count > 0 ? 0.75 : 0.2
                };
            }

            // Fixed Verified GeoJSON link source
            fetch('https://raw.githubusercontent.com/smb64/malaysia-geojson/main/malaysia.geojson')
                .then(response => response.json())
                .then(geoJsonData => {
                    L.geoJson(geoJsonData, {
                        style: styleFeature,
                        onEachFeature: function(feature, layer) {
                            const name = feature.properties.name || "Unknown State";
                            let checkName = name.toUpperCase().replace('W.P.', '').replace('WILAYAH PERSEKUTUAN', '').trim();
                            
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
                }).catch(err => console.error("GeoJSON loading broke: ", err));

        } catch(e) { console.error("Error setting up Qamarina's map engine:", e); }


        // ==========================================
        // 1. TOP 3 STATES CHART
        // ==========================================
        const top3Labels = Array.isArray(fullStateLabels) ? fullStateLabels.slice(0, 3) : [];
        const top3Data = Array.isArray(fullStateData) ? fullStateData.slice(0, 3) : [];

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
            options: { 
                responsive: true,
                scales: { y: { beginAtZero: true } } 
            }
        });

        // ==========================================
        // 2. SIZE DISTRIBUTION CHART
        // ==========================================
        const sizeRawKeys = {!! json_encode($sizeLabels ?? []) !!};
        const sizeRawValues = {!! json_encode($sizeData ?? []) !!};
        let groupedSize = { 'Small': 0, 'Other': 0 };
        
        if (Array.isArray(sizeRawKeys)) {
            sizeRawKeys.forEach((id, index) => {
                if (id == 1) groupedSize['Small'] += sizeRawValues[index] || 0;
                else groupedSize['Other'] += sizeRawValues[index] || 0;
            });
        }
        
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
        // 3. GENDER DISTRIBUTION CHART
        // ==========================================
        const genderRaw = {!! json_encode($genderData ?? []) !!};
        const genderArray = Array.isArray(genderRaw) ? genderRaw : [];
        
        new Chart(document.getElementById('genderChart'), {
            type: 'pie',
            data: {
                labels: genderArray.map(item => (item.Gender == 1 ? 'Female' : 'Male')),
                datasets: [{
                    data: genderArray.map(item => item.count),
                    backgroundColor: ['#fd35b0', '#0d6efd']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // ==========================================
        // 4. TREND LINE CHART
        // ==========================================
        const trendLabelsRaw = {!! json_encode($trendLabels ?? []) !!};
        const trendDataRaw = {!! json_encode($trendData ?? []) !!};

        let finalTrendLabels = Array.isArray(trendLabelsRaw) ? trendLabelsRaw : [];
        let finalTrendData = Array.isArray(trendDataRaw) ? trendDataRaw : [];

        if (finalTrendLabels.length === 1) {
            finalTrendLabels = ['', finalTrendLabels[0], ''];
            finalTrendData = [0, finalTrendData[0], 0];
        }

        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: finalTrendLabels,
                datasets: [{
                    label: 'Parcels',
                    data: finalTrendData,
                    borderColor: '#dc3545',
                    fill: true,
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.3,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: { 
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection