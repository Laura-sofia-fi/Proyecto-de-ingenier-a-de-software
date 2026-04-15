<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NATADINATTA')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #eef4ff;
            --panel: #ffffff;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #dbe5f2;
            --brand: #1d4ed8;
            --brand-dark: #0f172a;
        }
        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(96, 165, 250, .18), transparent 28%),
                linear-gradient(180deg, #f8fbff 0%, var(--bg) 100%);
            color: var(--ink);
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--brand-dark) 0%, var(--brand) 100%);
            box-shadow: inset -1px 0 0 rgba(255,255,255,.08);
        }
        .sidebar a,
        .mobile-menu a {
            color: rgba(255,255,255,.92);
            text-decoration: none;
            font-weight: 500;
            transition: .2s ease;
        }
        .sidebar a.active,
        .sidebar a:hover,
        .mobile-menu a.active,
        .mobile-menu a:hover {
            color: white;
            background: rgba(255,255,255,.14);
            border-radius: .9rem;
            transform: translateX(2px);
        }
        .stat-card,
        .content-card {
            border: 1px solid rgba(219,229,242,.95);
            border-radius: 1.1rem;
            background: var(--panel);
            box-shadow: 0 14px 40px rgba(15, 23, 42, .08);
        }
        .card-header {
            border-bottom: 1px solid var(--line) !important;
            border-top-left-radius: 1.1rem !important;
            border-top-right-radius: 1.1rem !important;
        }
        .table thead th {
            white-space: nowrap;
            color: var(--muted);
            font-size: .78rem;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .table tbody tr:hover {
            background: #f8fbff;
        }
        .btn {
            border-radius: .85rem;
            font-weight: 600;
        }
        .badge {
            border-radius: 999px;
            padding: .45rem .7rem;
        }
        .mobile-topbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            background: rgba(248, 251, 255, .92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(219,229,242,.95);
        }
        .mobile-topbar .brand-mark {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: .9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--brand-dark), var(--brand));
            color: white;
            font-weight: 800;
        }
        .mobile-menu {
            background: linear-gradient(180deg, var(--brand-dark) 0%, var(--brand) 100%);
            color: white;
        }
        .app-main {
            min-height: 100vh;
        }
        .page-header {
            gap: 1rem;
        }
        .filter-grid .btn,
        .toolbar-stack .btn,
        .action-group .btn {
            white-space: nowrap;
        }
        .action-group {
            display: inline-flex;
            gap: .5rem;
            align-items: center;
        }
        .table-responsive {
            border-radius: 1rem;
        }
        .table-mobile-tight td,
        .table-mobile-tight th {
            padding-top: .9rem;
            padding-bottom: .9rem;
        }
        .items-table {
            min-width: 720px;
        }
        @media (max-width: 991.98px) {
            .app-main {
                padding: 1.25rem !important;
            }
            .page-header h2 {
                font-size: 1.7rem;
            }
            .display-6 {
                font-size: 1.75rem;
            }
        }
        @media (max-width: 767.98px) {
            .app-main {
                padding: 1rem !important;
            }
            .content-card,
            .stat-card {
                border-radius: 1rem;
            }
            .page-header {
                flex-direction: column;
                align-items: stretch !important;
            }
            .page-header .header-actions {
                width: 100%;
                justify-content: space-between;
            }
            .table thead th {
                font-size: .72rem;
            }
            .filter-grid > div,
            .toolbar-stack > * {
                width: 100%;
            }
            .toolbar-stack,
            .action-group {
                flex-direction: column;
                align-items: stretch;
            }
            .action-group form {
                width: 100%;
            }
            .action-group .btn,
            .action-group form .btn {
                width: 100%;
            }
        }
        @media (max-width: 575.98px) {
            .page-header h2 {
                font-size: 1.5rem;
            }
            .display-6 {
                font-size: 1.5rem;
            }
            .card-body {
                padding: 1rem;
            }
            .mobile-topbar {
                padding-left: .25rem;
                padding-right: .25rem;
            }
        }
    </style>
</head>
<body>
@php
    $user = auth()->user();
    $navItems = [
        ['route' => 'dashboard', 'label' => 'Panel principal', 'href' => route('dashboard')],
        ['route' => 'orders.*', 'label' => 'Ventas y facturas', 'href' => route('orders.index')],
        ['route' => 'clients.*', 'label' => 'Clientes', 'href' => route('clients.index')],
    ];

    if ($user && $user->isAdmin()) {
        $navItems = array_merge($navItems, [
            ['route' => 'products.*', 'label' => 'Catálogo de productos', 'href' => route('products.index')],
            ['route' => 'users.*', 'label' => 'Usuarios', 'href' => route('users.index')],
            ['route' => 'receivables.*', 'label' => 'Cuentas por cobrar', 'href' => route('receivables.index')],
            ['route' => 'reports.*', 'label' => 'Reportes', 'href' => route('reports.index')],
            ['route' => 'settings.*', 'label' => 'Configuración', 'href' => route('settings.edit')],
        ]);
    }
@endphp

<div class="d-lg-none mobile-topbar">
    <div class="container-fluid py-3">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                    Menú
                </button>
                <div class="d-flex align-items-center gap-2">
                    <span class="brand-mark">N</span>
                    <div>
                        <div class="fw-bold">NATADINATTA</div>
                        <div class="small text-muted">Ventas al por mayor</div>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-dark btn-sm">Salir</button>
            </form>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-start mobile-menu" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header border-bottom border-light border-opacity-25">
        <div>
            <h5 class="offcanvas-title fw-bold mb-0" id="mobileSidebarLabel">NATADINATTA</h5>
            <div class="small text-white-50">Facturación y ventas</div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <div class="mb-4">
            <div class="fw-semibold">{{ $user?->name }}</div>
            <div class="small text-white-50 text-capitalize">{{ $user?->role }}</div>
        </div>
        <nav class="nav flex-column gap-2">
            @foreach($navItems as $item)
                <a class="px-3 py-2 {{ request()->routeIs($item['route']) ? 'active' : '' }}" href="{{ $item['href'] }}">{{ $item['label'] }}</a>
            @endforeach
        </nav>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <aside class="col-lg-3 col-xl-2 px-3 py-4 sidebar d-none d-lg-block">
            <h4 class="text-white fw-bold mb-1">NATADINATTA</h4>
            <p class="text-white-50 small mb-4">Facturación y ventas</p>

            <nav class="nav flex-column gap-2">
                @foreach($navItems as $item)
                    <a class="px-3 py-2 {{ request()->routeIs($item['route']) ? 'active' : '' }}" href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                @endforeach
            </nav>
        </aside>

        <main class="col-12 col-lg-9 col-xl-10 p-4 p-lg-5 app-main">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 page-header">
                <div>
                    <h2 class="fw-bold mb-1">@yield('page-title', 'NATADINATTA')</h2>
                    <div class="text-muted">@yield('page-description', 'Sistema de facturación y gestión de ventas')</div>
                </div>
                <div class="d-none d-lg-flex align-items-center gap-3 header-actions">
                    <div class="text-end">
                        <div class="fw-semibold">{{ $user?->name }}</div>
                        <div class="text-muted small text-capitalize">{{ $user?->role }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-dark">Cerrar sesión</button>
                    </form>
                </div>
            </div>

            @include('partials.alerts')
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
