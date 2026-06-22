<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>NinjaVault System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jqvmap.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqvmap/1.5.1/jquery.vmap.min.js"></script>
    
    <script src="{{ asset('js/jquery.vmap.malaysia.js') }}"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap');

        /* Full-page setup with your base style, adding his readable font colors */
        body { 
            background: #f6f7fb; 
            min-height: 100vh; 
            font-family: 'Inter', sans-serif; 
            color: #2d2d2d !important;
        }
        
        .sidebar {
            width: 260px;
            height: 100vh;
            background: #ffffff;
            position: fixed;
            border-right: 1px solid #e0e0e0;
            padding: 20px;
            z-index: 1000;
        }
        
        .main-content { margin-left: 260px; padding: 30px; }
        
        /* Navigation Links with both your core layouts and his custom hover extensions */
        .nav-link { 
            color: #2d2d2d !important; 
            padding: 12px 15px; 
            border-radius: 8px; 
            margin-bottom: 8px; 
            font-weight: 500; 
            text-decoration: none; 
            display: block; 
            transition: all 0.2s; 
        }
        .nav-link:hover { background: #fff5f5; color: #dc3545 !important; }
        .nav-link.active { background: #dc3545; color: #fff !important; box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3); }
        .nav-link.active:hover { background: #b02a37 !important; color: #fff !important; }
        
        /* Branding Header Styles */
        .sidebar-brand { 
            font-size: 1.5rem; 
            font-weight: 800; 
            color: #1e293b; 
            text-decoration: none; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            gap: 10px;
            margin-bottom: 40px; 
            padding: 5px 0;
            background: #ffffff;
        }
        .sidebar-brand img {
            transition: transform 0.2s ease-in-out;
            display: inline-block;
        }
        .sidebar-brand:hover img {
            transform: scale(1.08);
        }
        
        /* Map Styles */
        #vmap-malaysia {
            width: 100%;
            height: 400px;
            position: relative;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        #vmap-malaysia svg {
            width: 100% !important;
            height: 100% !important;
        }
        .jqvmap-zoomin, .jqvmap-zoomout { 
            width: 24px !important; 
            height: 24px !important; 
            background: #dc3545 !important; 
            line-height: 22px !important;
            text-align: center;
        }
        .jqvmap-label {
            background: #212529 !important;
            color: white !important;
            padding: 8px 12px !important;
            border-radius: 4px !important;
            font-size: 13px !important;
            z-index: 9999 !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        /* --- HIS CONTROLLER INJECTED MODERN ROUNDED UI CHANGES --- */
        .card, .table, .form-control, .btn, .alert, .list-group-item, .modal-content {
            border-radius: 16px !important;
        }
        .table {
            border-radius: 16px;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
        }
        .btn {
            border-radius: 40px !important;
            padding: 8px 20px;
            font-weight: 500;
        }
        .form-control, .form-select {
            border-radius: 12px !important;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.2);
        }
        .table thead th:first-child { border-top-left-radius: 16px; }
        .table thead th:last-child { border-top-right-radius: 16px; }
        .table tbody tr:last-child td:first-child { border-bottom-left-radius: 16px; }
        .table tbody tr:last-child td:last-child { border-bottom-right-radius: 16px; }

        .badge {
            border-radius: 40px;
            padding: 6px 12px;
            font-weight: 500;
        }
        .container .card {
            border: none;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .container .card:hover {
            box-shadow: 0 12px 28px rgba(0,0,0,0.1);
        }
        .btn-sm {
            border-radius: 20px !important;
            padding: 4px 12px;
        }
        
        /* Modernized Grey/Dark Readable Typography Rules */
        h1, h2, h3, h4, h5, h6 { color: #1e1e1e !important; }
        .table td, .table th { color: #2d2d2d !important; }
        ::placeholder { color: #888 !important; opacity: 1; }

        /* Custom Outline Red Button Rules */
        .btn-outline-red {
            background-color: #ffffff !important;
            color: #2d2d2d !important;
            border: 1px solid #dc3545 !important;
            transition: all 0.2s ease;
            border-radius: 40px;
            padding: 8px 20px;
            font-weight: 500;
        }
        .btn-outline-red:hover {
            background-color: #dc3545 !important;
            color: #ffffff !important;
            border-color: #dc3545 !important;
        }
    </style>
</head>
<body>

<div class="sidebar shadow-sm">
    <a href="/" class="sidebar-brand">
        <img src="{{ asset('images/ninja-vault-logo.png') }}" 
             alt="NinjaVault Logo" 
             style="height: 38px; width: auto; object-fit: contain; mix-blend-mode: multiply;">
        <span>Ninja<span style="color: #dc3545;">Vault</span></span>
    </a>
    
    <nav class="nav flex-column">
        <a class="nav-link {{ request()->is('/') || request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-grid-1x2-fill me-2"></i> MAIN PAGE
        </a>

        <a class="nav-link {{ request()->is('feedback') ? 'active' : '' }}" href="{{ route('feedback') }}">
            <i class="bi bi-shield-check me-2"></i> SERVICE QUALITY
        </a>

        <a class="nav-link {{ request()->is('chatbot') ? 'active' : '' }}" href="{{ route('chatbot') }}">
            <i class="bi bi-chat-dots me-2"></i> CHATBOT
        </a>

        <a class="nav-link {{ request()->routeIs('admin.lockers.*') ? 'active' : '' }}" href="{{ route('admin.lockers.index') }}">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i> LOCKERS
        </a>

        <a class="nav-link {{ request()->routeIs('admin.assignments.*') ? 'active' : '' }}" href="{{ route('admin.assignments.index') }}">
            <i class="bi bi-box-arrow-in-right me-2"></i> ASSIGN PARCEL
        </a>

        <a class="nav-link {{ request()->routeIs('pickup.*') ? 'active' : '' }}" href="{{ route('pickup.form') }}">
            <i class="bi bi-box-seam me-2"></i> PICKUP
        </a>
    </nav>
</div>

<div class="main-content">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')

</body>
</html>