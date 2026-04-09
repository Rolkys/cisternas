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

    <nav class="navbar navbar-expand-lg navbar-dark shadow" style="position: fixed; top: 0; left: 0; right: 0; z-index: 1060; width: 100vw; background-color: #e10734 !important; min-height: 72px;">

        <div class="container-fluid px-3">

            <a class="navbar-brand fw-bold d-flex align-items-center gap-2"
                href="{{ route('cisterna.index') }}"
                style="font-size: 1.3rem;">
                <img src="{{ asset('images/anagrama.png') }}" alt="MG" height="44">
                <span>Control Cisternas</span>
            </a>

            <div class="ms-auto d-flex align-items-center gap-2 flex-wrap" style="height: 100%;">
                <button type="button" id="theme-toggle" class="btn btn-sm btn-ghost text-white btn-theme-toggle" aria-label="Cambiar tema" style="font-size: 1.2rem; width: 44px; height: 44px;">
                    <i id="theme-toggle-icon" class="bi bi-moon-stars-fill"></i>
                </button>
                @auth
                    <span class="text-white fw-semibold" style="font-size: 1rem;">{{ auth()->user()->email }}</span>
                    <span class="badge fs-6 px-3 py-2" style="background-color: rgba(0,0,0,0.3);">{{ auth()->user()->role }}</span>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-light btn-lg" style="font-size: 1rem; padding: 0.5rem 1rem;">
                            <i class="bi bi-people"></i> <span class="d-none d-md-inline">Usuarios</span>
                        </a>
                    @endif
                    
                    <a href="{{ route('login') }}" 
                    class="btn btn-outline-light btn-lg" 
                    style="font-size: 1rem; padding: 0.5rem 1rem;"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i> <span class="d-none d-md-inline">Salir</span>
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf

                    </form>
                @endauth
            </div>
        </div>
    </nav>

    {{-- ════════════════════════ NOTIFICACIONES ════════════════════════ --}}
    <div class="container-fluid" style="padding-top: 10px;">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {!! session('success') !!}
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
    <main class="container-fluid py-3" style="padding-top: 10px !important; margin: 0;">
        @yield('content')
    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- JS propio (separado del layout) --}}
    <script src="{{ asset('js/app-custom.js') }}"></script>
</body>
</html>