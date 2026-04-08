<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Cisternas</title>
    <script>
        (function () {
            try {
                var theme = localStorage.getItem('app-theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var isDark = theme === 'dark' || (!theme && prefersDark);
                if (isDark) {
                    document.documentElement.classList.add('dark-mode');
                }
            } catch (e) {}
        })();
    </script>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Estilos propios (separados del layout) --}}
    <link href="{{ asset('css/app-custom.css') }}" rel="stylesheet">
</head>
<body class="min-vh-100" style="background: white;">

    {{-- ════════════════════════════ NAVBAR ════════════════════════════ --}}

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow" style="position: fixed; top: 0; left: 0; right: 0; z-index: 1060; width: 100vw;">

        <div class="container-fluid">

            <a class="navbar-brand fw-bold d-flex align-items-center gap-2"
                href="{{ route('cisterna.index') }}">
                <img src="{{ asset('images/anagrama.png') }}" alt="MG" height="36">
                <span>Control Cisternas</span>
            </a>

<div class="ms-auto d-flex align-items-center gap-2" style="height: 100%;">
                <button type="button" id="theme-toggle" class="btn btn-sm btn-ghost btn-theme-toggle text-white" aria-label="Cambiar tema">
                    <i id="theme-toggle-icon" class="bi bi-moon-stars-fill"></i>
                </button>
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

    {{-- ════════════════════════ NOTIFICACIONES ════════════════════════ --}}
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

    {{-- ══════════════════════════ CONTENIDO ════════════════════════════ --}}
<main class="container-fluid py-3" style="padding-top: 80px; margin: 0;">
        @yield('content')
    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- JS propio (separado del layout) --}}
    <script src="{{ asset('js/app-custom.js') }}"></script>
</body>
</html>
