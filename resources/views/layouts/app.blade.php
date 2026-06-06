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

        body { background:#f6f7fb; min-height: 100vh; font-family: 'Inter', sans-serif; }
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
        .nav-link { color: #555; padding: 12px 15px; border-radius: 8px; margin-bottom: 8px; font-weight: 500; text-decoration: none; display: block; transition: all 0.2s; }
        .nav-link:hover { background: #fff5f5; color: #dc3545; }
        .nav-link.active { background: #dc3545; color: #fff !important; box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3); }
        
        /* --- LOGO BRAND BRANDING UPDATES --- */
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
            background: #ffffff; /* Explicit background for blending context */
        }
        .sidebar-brand img {
            transition: transform 0.2s ease-in-out;
            display: inline-block;
        }
        .sidebar-brand:hover img {
            transform: scale(1.08);
        }
        
        /* --- MAP FIXES --- */
        #vmap-malaysia {
            width: 100%;
            height: 400px;
            position: relative;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        /* Prevent SVG from being tiny dots */
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
    </style>
</head>
<body>

<div class="sidebar shadow-sm" style="background: #ffffff;">
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
    </nav>
</div>

<div class="main-content">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- This allows the dashboard to inject its custom scripts here --}}
@stack('scripts')

</body>
</html>