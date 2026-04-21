<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Chama') — Smart Chama</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-logo">
            <div class="brand-icon"><i class="bi bi-coin"></i></div>
            <div>
                <div class="brand-name">SmartChama</div>
                <div class="brand-tagline">Savings Platform</div>
            </div>
        </a>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>

        <div class="nav-item">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('contributions.index') }}"
               class="nav-link {{ request()->routeIs('contributions.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i> Contributions
            </a>
        </div>

        <div class="nav-item">
            <a href="{{ route('loans.index') }}"
               class="nav-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                <i class="bi bi-bank"></i> Loans
            </a>
        </div>

        @if(auth()->user()->role === 'treasurer' || auth()->user()->role === 'admin')
        <div class="nav-section-label">Management</div>

        <div class="nav-item">
            <a href="{{ route('reports.index') }}"
               class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i> Reports
            </a>
        </div>
        @endif

        @if(auth()->user()->role === 'admin')
    <div class="nav-section-label">Admin</div>
 
    <div class="nav-item">
        <a href="{{ route('admin.members') }}"
           class="nav-link {{ request()->routeIs('admin.members') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Members
            @php $pending = \App\Models\User::where('chama_id', auth()->user()->chama_id)->where('status','pending')->count(); @endphp
            @if($pending > 0)
                <span class="nav-badge">{{ $pending }}</span>
            @endif
        </a>
    </div>
 
    <div class="nav-item">
        <a href="{{ route('admin.audit') }}"
           class="nav-link {{ request()->routeIs('admin.audit') ? 'active' : '' }}">
            <i class="bi bi-shield-check"></i> Audit Logs
        </a>
    </div>
 
    {{-- ADD THIS NEW LINK --}}
    <div class="nav-item">
        <a href="{{ route('chama.settings') }}"
           class="nav-link {{ request()->routeIs('chama.settings') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Chama Settings
        </a>
    </div>
@endif

        <div class="nav-section-label">Account</div>
 
        <div class="nav-item">
            <a href="{{ route('billing.plans') }}"
               class="nav-link {{ request()->routeIs('billing.*') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i> Plans & Billing
                @if(auth()->user()->chama?->isOnFreePlan())
                    <span class="nav-badge" style="background:#d97706">Free</span>
                @endif
            </a>
        </div>
 
    </nav>

    <div class="sidebar-user">
        <div class="tp-avatar">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div style="min-width:0">
            <div class="user-name">{{ auth()->user()->name }}</div>
            <div class="user-role">{{ auth()->user()->role }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="logout-btn" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</aside>

<!-- ── Main Wrapper ──────────────────────────────────────────────────── -->
<div class="main-wrapper">

    <!-- Topbar -->
    <header class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <div class="topbar-left">
                <h1>@yield('page-title', 'Dashboard')</h1>
                <p>@yield('page-subtitle', auth()->user()->chama->name ?? 'Smart Chama')</p>
            </div>
        </div>
        <div class="topbar-right">
            <a href="{{ route('contributions.create') }}" class="btn-primary-custom" style="font-size:12px;padding:7px 14px">
                <i class="bi bi-plus-lg"></i> Contribute
            </a>
            <a href="#" class="topbar-btn" title="Notifications">
                <i class="bi bi-bell"></i>
                <span class="notif-dot"></span>
            </a>
            <a href="{{ route('profile.edit') }}" class="topbar-profile">
                <div class="tp-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <span class="tp-name">{{ explode(' ', auth()->user()->name)[0] }}</span>
                <i class="bi bi-chevron-down" style="font-size:10px;color:var(--text-muted)"></i>
            </a>
        </div>
    </header>

    <!-- Page Content -->
    <main class="page-content">

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="alert-custom alert-success">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert-custom alert-danger">
            <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
        </div>
        @endif

        @yield('content')
    </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
    }
    // Close sidebar on outside click (mobile)
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('sidebar');
        if (window.innerWidth <= 768 && !sidebar.contains(e.target) && sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
        }
    });
</script>

@stack('scripts')
</body>
</html>


