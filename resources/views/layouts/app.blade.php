<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Cisternas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #c71b1b;
            --primary-dark: #a31515;
            --secondary: #0f2130;
        }
        body { background: #f8f9fa; }
        .navbar { background-color: var(--secondary) !important; }
        .navbar-brand, .nav-link { color: #fff !important; }
        .btn-primary { background-color: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background-color: var(--primary-dark); border-color: var(--primary-dark); }
        .table thead { background-color: var(--secondary); color: #fff; }
        .table > tbody > tr.row-consumida  > td { background-color: #adebb3 !important; }
        .table > tbody > tr.row-incidencia > td { background-color: #FF746C !important; }
        .table > tbody > tr.row-hoy        > td { background-color: #90D5FF !important; }
        .table > tbody > tr.row-futura     > td { background-color: #FFEE8C !important; }
        .table > tbody > tr.row-pendiente  > td { background-color: #e9ecef !important; }
        
        /* Colores de fila según estado — igual que tu site.css */
        .row-consumida  { background-color: #adebb3 !important; }
        .row-incidencia { background-color: #FF746C !important; }
        .row-hoy        { background-color: #90D5FF !important; }
        .row-futura     { background-color: #FFEE8C !important; }
        .row-pendiente  { background-color: #e9ecef !important; }
    </style>
</head>
<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('cisterna.index') }}">
                <img src="{{ asset('images/anagrama.png') }}" alt="MG" height="36">
                <span>Control Cisternas</span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-3">
                @auth
                    <span class="text-white small">{{ auth()->user()->email }}</span>
                    <span class="badge bg-secondary">{{ auth()->user()->role }}</span>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-light">
                            <i class="bi bi-people"></i> Usuarios
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-light">
                            <i class="bi bi-box-arrow-right"></i> Salir
                        </button>
                    </form>
                    

                @endauth
            </div>
        </div>
    </nav>

    {{-- Notificaciones (equivalente a tu snackbar) --}}
    <div class="container-fluid mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- Contenido de cada vista --}}
    <main class="container-fluid py-3">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>