<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sistem Informasi Posyandu')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #1e40af;
            --secondary-color: #22c55e;
            --accent-color: #f97316;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --success-color: #10b981;
            --info-color: #0ea5e9;
            --light-color: #f3f4f6;
            --dark-color: #1f2937;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f9fafb;
        }
        
        main {
            flex: 1;
        }
        
        .navbar-brand {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
        }
        
        .btn-success {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-success:hover,
        .btn-success:focus {
            background-color: #16a34a;
            border-color: #16a34a;
        }
        
        .btn-accent {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
        }
        
        .btn-accent:hover,
        .btn-accent:focus {
            background-color: #ea580c;
            border-color: #ea580c;
            color: white;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .text-secondary {
            color: var(--secondary-color) !important;
        }
        
        .text-accent {
            color: var(--accent-color) !important;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .bg-secondary {
            background-color: var(--secondary-color) !important;
        }
        
        .bg-accent {
            background-color: var(--accent-color) !important;
        }
        
        .bg-light-primary {
            background-color: #dbeafe !important;
        }
        
        .card {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #f3f4f6;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .sidebar {
            background-color: white;
            min-height: calc(100vh - 56px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            padding-top: 1rem;
        }
        
        .sidebar-sticky {
            position: sticky;
            top: 1rem;
        }
        
        .sidebar .nav-link {
            color: var(--dark-color);
            padding: 0.75rem 1.5rem;
            margin-bottom: 0.25rem;
            border-radius: 0.375rem;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            background-color: #f3f4f6;
        }
        
        .sidebar .nav-link.active {
            background-color: #dbeafe;
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 1.25rem;
            text-align: center;
        }
        
        .sidebar-heading {
            padding: 0.75rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            font-weight: 600;
        }
        
        .dashboard-stat {
            padding: 1.5rem;
            border-radius: 0.5rem;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }
        
        .dashboard-stat:hover {
            transform: translateY(-5px);
        }
        
        .dashboard-stat .stat-count {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .dashboard-stat .stat-title {
            font-size: 1rem;
            font-weight: 500;
            opacity: 0.9;
        }
        
        .dashboard-stat .stat-icon {
            font-size: 2.5rem;
            opacity: 0.3;
            position: absolute;
            top: 1rem;
            right: 1rem;
        }
        
        .bg-stat-1 {
            background: linear-gradient(120deg, #1e40af, #3b82f6);
        }
        
        .bg-stat-2 {
            background: linear-gradient(120deg, #16a34a, #22c55e);
        }
        
        .bg-stat-3 {
            background: linear-gradient(120deg, #ea580c, #f97316);
        }
        
        .bg-stat-4 {
            background: linear-gradient(120deg, #0369a1, #0ea5e9);
        }
        
        .table thead th {
            background-color: #f9fafb;
            color: #4b5563;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }
        
        .alert {
            border-radius: 0.5rem;
            border: none;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        
        .alert-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .alert-info {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .badge-success {
            background-color: var(--success-color);
        }
        
        .badge-danger {
            background-color: var(--danger-color);
        }
        
        .badge-warning {
            background-color: var(--warning-color);
        }
        
        .badge-info {
            background-color: var(--info-color);
        }
        
        .badge-primary {
            background-color: var(--primary-color);
        }
        
        .badge-secondary {
            background-color: var(--secondary-color);
        }
        
        footer {
            background-color: white;
            border-top: 1px solid #f3f4f6;
            padding: 1rem 0;
            margin-top: 2rem;
        }
        
        .avatar {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 3px solid white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .avatar-sm {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid white;
        }
        
        .profile-header {
            background-color: #dbeafe;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0.5rem;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
            
            .dashboard-stat {
                margin-bottom: 1.5rem;
            }
        }
        
        /* Animasi */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .btn-hover-scale {
            transition: transform 0.3s;
        }
        
        .btn-hover-scale:hover {
            transform: scale(1.05);
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="fas fa-heartbeat me-2"></i>
                    {{ config('app.name', 'Sistem Informasi Posyandu') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->nama }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user-edit me-2"></i>
                                        {{ __('Profil') }}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @auth
                <div class="container">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="sidebar">
                                <div class="sidebar-sticky">
                                    <div class="sidebar-heading">
                                        Menu Utama
                                    </div>
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                                <i class="fas fa-tachometer-alt"></i>
                                                Dashboard
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('balita.*') ? 'active' : '' }}" href="{{ route('balita.index') }}">
                                                <i class="fas fa-baby"></i>
                                                Data Balita
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('ibu-hamil.*') ? 'active' : '' }}" href="{{ route('ibu-hamil.index') }}">
                                                <i class="fas fa-female"></i>
                                                Data Ibu Hamil
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('jadwal-kegiatan.*') ? 'active' : '' }}" href="{{ route('jadwal-kegiatan.index') }}">
                                                <i class="fas fa-calendar-alt"></i>
                                                Jadwal Kegiatan
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('pengumuman.*') ? 'active' : '' }}" href="{{ route('pengumuman.index') }}">
                                                <i class="fas fa-bullhorn"></i>
                                                Pengumuman
                                            </a>
                                        </li>
                                    </ul>
                                    
                                    <div class="sidebar-heading mt-3">
                                        Laporan
                                    </div>
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('laporan.index') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                                                <i class="fas fa-file-alt"></i>
                                                Laporan
                                            </a>
                                        </li>
                                    </ul>
                                    
                                    @if(auth()->user()->isAdmin())
                                    <div class="sidebar-heading mt-3">
                                        Administrasi
                                    </div>
                                    <ul class="nav flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                                <i class="fas fa-users"></i>
                                                Manajemen User
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('posyandu.*') ? 'active' : '' }}" href="{{ route('posyandu.index') }}">
                                                <i class="fas fa-clinic-medical"></i>
                                                Data Posyandu
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('imunisasi.*') || request()->routeIs('vitamin.*') ? 'active' : '' }}" href="{{ route('imunisasi.index') }}">
                                                <i class="fas fa-syringe"></i>
                                                Imunisasi & Vitamin
                                            </a>
                                        </li>
                                    </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            @include('layouts.partials.alert')
                            
                            @yield('content')
                        </div>
                    </div>
                </div>
            @else
                <div class="container">
                    @include('layouts.partials.alert')
                    
                    @yield('content')
                </div>
            @endauth
        </main>
        
        <footer class="text-center text-muted">
            <div class="container">
                <p class="mb-0">&copy; {{ date('Y') }} Sistem Informasi Posyandu. Hak Cipta Dilindungi.</p>
            </div>
        </footer>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Enable tooltips
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            
            // Fade in elements with fade-in class
            document.querySelectorAll('.fade-in').forEach(function(element) {
                element.classList.add('show');
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>