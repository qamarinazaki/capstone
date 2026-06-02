@extends('layouts.app')

@section('content')
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
                    </select>
                </div>
                <div>
                    <label class="filter-label d-block text-muted small fw-bold">SELECT MONTH</label>
                    <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ $selectedMonth == 'all' ? 'selected' : '' }}>All Months</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                                {{ date("F", mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">TOTAL PARCELS</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($totalParcel) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">TOTAL WEIGHT</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($totalWeight,2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">AVERAGE WEIGHT</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($avgWeight,2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3 shadow-sm border-0">
                <div class="text-muted small fw-bold">DELIVERED (APPROX)</div>
                <div class="h3 fw-bold text-dark mb-0">{{ number_format($delivered) }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card p-3 shadow-sm border-0">
                <h5 class="fw-bold mb-3">Geographical Distribution (Malaysia)</h5>
                
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
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
                </style>
                
                <div id="malaysiaMap"></div>
            </div>
        </div>
    </div>

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

    <div class="card p-3 shadow-sm border-0">
        <h5 class="fw-bold mb-3">Latest Parcels ({{ $selectedYear }} {{ $selectedMonth !== 'all' ? '- Month '.$selectedMonth : '' }})</h5>
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
                        
                        if($selectedMonth !== 'all') {
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

<script>
    // =========================================================
    // 0. CHOROPLETH MALAYSIA REGIONAL MAP LOGIC
    // =========================================================
    
    const mapLabels = {!! json_encode($stateLabels) !!}; 
    const mapData = {!! json_encode($stateData) !!};     

    /**
     * Standardize nama negeri untuk padanan database & GeoJSON
     */
    function normalizeStateKey(name) {
        if (!name) return '';
        let clean = name.toLowerCase().trim();
        
        if (clean === 'pulau pinang' || clean === 'penang') return 'penang';
        if (clean === 'wp kuala lumpur' || clean === 'kuala lumpur') return 'kuala lumpur';
        if (clean === 'wp putrajaya' || clean === 'putrajaya') return 'putrajaya';
        if (clean === 'wp labuan' || clean === 'labuan') return 'labuan';
        if (clean === 'malacca' || clean === 'melaka') return 'melaka';
        return clean;
    }

    const parcelDataByState = {};
    mapLabels.forEach((state, index) => {
        if(state) {
            const cleanName = normalizeStateKey(state);
            parcelDataByState[cleanName] = mapData[index];
        }
    });

    const map = L.map('malaysiaMap').setView([4.2105, 109.5554], 6); 

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap &copy; CARTO'
    }).addTo(map);

    const maxParcels = Math.max(...mapData, 1);

    function getColorIntensity(stateName) {
        const count = parcelDataByState[stateName.toLowerCase().trim()] || 0;
        if (count === 0) return '#e9ecef'; 
        
        const ratio = count / maxParcels;
        if (ratio > 0.8) return '#990011'; 
        if (ratio > 0.5) return '#dc3545'; 
        if (ratio > 0.2) return '#ea6b76'; 
        return '#f4b2b7';                  
    }

    const malaysiaGeoJSON = {
        "type": "FeatureCollection",
        "features": [
            { "type": "Feature", "id": "01", "properties": { "name": "johor" }, "geometry": { "type": "MultiPolygon", "coordinates": [[[[103.7,1.4],[103.6,1.4],[103.5,1.6],[102.7,2.0],[102.5,2.5],[103.8,2.7],[104.3,1.4],[103.7,1.4]]]] } },
            { "type": "Feature", "id": "02", "properties": { "name": "kedah" }, "geometry": { "type": "MultiPolygon", "coordinates": [[[[100.3,6.4],[100.6,6.3],[101.0,5.8],[100.5,5.1],[100.3,5.4],[100.2,6.1],[100.3,6.4]]]] } },
            { "type": "Feature", "id": "03", "properties": { "name": "kelantan" }, "geometry": { "type": "MultiPolygon", "coordinates": [[[[102.1,6.2],[102.4,5.8],[102.4,4.7],[101.3,4.6],[101.4,5.5],[101.8,6.1],[102.1,6.2]]]] } },
            { "type": "Feature", "id": "04", "properties": { "name": "melaka" }, "geometry": { "type": "MultiPolygon", "coordinates": [[[[102.1,2.2],[102.4,2.4],[102.7,2.3],[102.5,2.0],[102.1,2.2]]]] } },
            { "type": "Feature", "id": "05", "properties": { "name": "negeri sembilan" }, "geometry": { "type": "MultiPolygon", "coordinates": [[[[101.9,3.1],[102.4,3.0],[102.7,2.4],[102.4,2.4],[101.7,2.6],[101.9,3.1]]]] } },
            { "type": "Feature", "id": "06", "properties": { "name": "pahang" }, "geometry": { "type": "MultiPolygon", "coordinates": [[[[102.9,4.7],[103.4,4.1],[103.5,3.5],[103.6,2.6],[102.4,3.0],[101.9,3.1],[101.7,3.5],[102.1,4.7],[102.9,4.7]]]] } },
            { "type": "Feature", "id": "07", "properties": { "name": "penang" }, "geometry": { "type": "MultiPolygon", "coordinates": [[[[100.4,5.5],[100.5,5.1],[100.2,5.2],[100.2,5.5],[100.4,5.5]]]] } },
            { "type": "Feature", "id": "08", "properties": { "name": "perak" }, "geometry": { "type": "MultiPolygon", "coordinates": [[[[101.0,5.8],[101.7,5.5],[101.3,4.6],[102.1,4.7],[101.7,3.5],[100.9,3.7],[100.4,4.9],[101.0,5.8]]]] } },
            { "type": "Feature", "id": "09", "properties": { "name": "perlis" }, "geometry": { "type": "MultiPolygon", "coordinates": [[[[100.2,6.7],[100.3,6.4],[100.1,6.4],[100.2,6.7]]]] } },
            { "type": "Feature", "id": "10", "properties": { "name": "selangor" }, "geometry": { "type": "MultiPolygon", "coordinates": [[[[101.7,3.5],[101.9,3.1],[101.7,2.6],[101.3,2.9],[100.8,3.3],[101.3,3.8],[101.7,3.5]]]] } },
            { "type": "Feature", "id": "11", "properties": { "name": "terengganu" }, "geometry": { "type": "MultiPolygon", "coordinates": [[[[102.5,5.8],[103.5,5.3],[103.4,4.1],[102.9,4.7],[102.5,5.8]]]] } },
            { "type": "Feature", "id": "12", "properties": { "name": "sabah" }, "geometry": { "type": "MultiPolygon", "coordinates": [[[[116.0,7.2],[117.2,6.7],[119.3,5.1],[118.2,4.2],[115.3,4.8],[115.7,6.1],[116.0,7.2]]]] } },
            { "type": "Feature", "id": "13", "properties": { "name": "sarawak" }, "geometry": { "type": "MultiPolygon", "coordinates": [[[[115.3,4.8],[115.4,3.8],[113.8,1.5],[111.4,1.0],[109.6,1.8],[110.5,1.7],[111.9,3.2],[113.9,4.4],[115.3,4.8]]]] } },
            { "type": "Feature", "id": "14", "properties": { "name": "kuala lumpur" }, "geometry": { "type": "MultiPolygon", "coordinates": [[[[101.65,3.2],[101.75,3.2],[101.75,3.1],[101.65,3.1],[101.65,3.2]]]] } }
        ]
    };

    let geojsonLayer = L.geoJson(malaysiaGeoJSON, {
        style: function(feature) {
            return {
                fillColor: getColorIntensity(normalizeStateKey(feature.properties.name)),
                weight: 2,
                opacity: 1,
                color: '#ffffff', 
                fillOpacity: 0.85
            };
        },
        onEachFeature: function(feature, layer) {
            const rawName = feature.properties.name;
            const normName = normalizeStateKey(rawName);
            const count = parcelDataByState[normName] || 0;

            let displayName = rawName.toUpperCase();
            if (displayName === 'PENANG') displayName = 'PULAU PINANG';
            if (displayName === 'KUALA LUMPUR') displayName = 'WP KUALA LUMPUR';

            layer.bindPopup(`<div style="text-align:center;"><strong>${displayName}</strong><br><span style="color:#dc3545; font-size:14px; font-weight:bold;">${count.toLocaleString()} Parcels</span></div>`);

            layer.on({
                mouseover: function(e) {
                    var l = e.target;
                    l.setStyle({
                        weight: 3,
                        color: '#212529',
                        fillOpacity: 0.95
                    });
                    l.bringToFront();
                },
                mouseout: function(e) {
                    geojsonLayer.resetStyle(e.target);
                }
            });
        }
    }).addTo(map);

    map.fitBounds(geojsonLayer.getBounds());

    // =========================================================
    // CHART.JS ENGINES MANAGEMENT LOGIC
    // =========================================================
    
    // 1. TOP 3 STATES CHART (DIBETULKAN: Guna slice(0,3) untuk kekalkan paparan Top 3)
    const fullStateLabels = {!! json_encode($stateLabels) !!};
    const fullStateData = {!! json_encode($stateData) !!};

    const top3Labels = fullStateLabels.slice(0, 3);
    const top3Data = fullStateData.slice(0, 3);

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
        options: { scales: { y: { beginAtZero: true } } }
    });

    // 2. PARCEL SIZE CHART
    const sizeRawKeys = {!! json_encode($sizeLabels) !!};
    const sizeRawValues = {!! json_encode($sizeData) !!};
    let groupedSize = { 'Small': 0, 'Other': 0 };
    sizeRawKeys.forEach((id, index) => {
        if (id == 1) groupedSize['Small'] += sizeRawValues[index];
        else groupedSize['Other'] += sizeRawValues[index];
    });

    new Chart(document.getElementById('sizeChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(groupedSize),
            datasets: [{
                data: Object.values(groupedSize),
                backgroundColor: ['#dc3545', '#6c757d']
            }]
        }
    });

    // 3. GENDER CHART
    const genderRaw = {!! json_encode($genderData) !!};
    const genderLabels = genderRaw.map(item => (item.Gender == 1 ? 'Female' : 'Male'));
    const genderCounts = genderRaw.map(item => item.count);

    new Chart(document.getElementById('genderChart'), {
        type: 'pie',
        data: {
            labels: genderLabels,
            datasets: [{
                data: genderCounts,
                backgroundColor: ['#0d6efd', '#fd35b0']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 4. TREND CHART
    const trendLabels = {!! json_encode($trendLabels) !!};
    const trendData = {!! json_encode($trendData) !!};

    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Parcels',
                data: trendData,
                fill: true,
                tension: 0.3,
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                borderColor: '#dc3545',
                pointBackgroundColor: '#dc3545'
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });
</script>
@endsection