<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Superadmin') — Smart Chama Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @stack('styles')
</head>
<body>

<div class="sidebar" id="sidebar" style="background:#1e1b4b">
    <div class="sidebar-brand">
        <a href="{{ route('superadmin.dashboard') }}" class="brand-logo">
            <div class="brand-icon" style="background:#7c3aed">
                <i class="bi bi-shield-fill"></i>
            </div>
            <div>
                <div class="brand-name">SuperAdmin</div>
                <div class="brand-tagline">Platform Control</div>
            </div>
        </a>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Platform</div>
        <div class="nav-item">
            <a href="{{ route('superadmin.dashboard') }}"
               class="nav-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('superadmin.chamas') }}"
               class="nav-link {{ request()->routeIs('superadmin.chamas') ? 'active' : '' }}">
                <i class="bi bi-people"></i> All Chamas
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('superadmin.subscriptions') }}"
               class="nav-link {{ request()->routeIs('superadmin.subscriptions') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i> Subscriptions
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('superadmin.revenue') }}"
               class="nav-link {{ request()->routeIs('superadmin.revenue') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> Revenue
            </a>
        </div>
        <div class="nav-item">
            <a href="{{ route('superadmin.plans') }}"
               class="nav-link {{ request()->routeIs('superadmin.plans') ? 'active' : '' }}">
                <i class="bi bi-layers"></i> Plans
            </a>
        </div>
    </nav>
    <div class="sidebar-user">
        <div class="user-avatar">
            {{ strtoupper(substr(auth('superadmin')->user()->name ?? 'SA', 0, 2)) }}
        </div>
        <div class="sidebar-user-info">
            <div class="user-name">{{ auth('superadmin')->user()->name ?? 'Superadmin' }}</div>
            <div class="user-role">Superadmin</div>
        </div>
        <form method="POST" action="{{ route('superadmin.logout') }}">
            @csrf
            <button type="submit" class="logout-btn" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</div>

<div class="main-wrapper">
    <header class="topbar">
        <div class="topbar-left-group">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <div class="topbar-left">
                <h1>@yield('page-title', 'Dashboard')</h1>
                <p>Smart Chama Platform</p>
            </div>
        </div>
        <div class="topbar-right">
            <span class="badge-custom badge-primary">Platform Admin</span>
        </div>
    </header>

    <main class="page-content">
        @if(session('success'))
            <div class="alert-custom alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="alert-custom alert-danger">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}
</script>
@stack('scripts')
</body>
</html>