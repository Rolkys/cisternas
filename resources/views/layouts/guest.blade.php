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
    {{-- Estilos propios --}}
    <link href="{{ asset('css/app-custom.css') }}" rel="stylesheet">
</head>
{{-- IV: Color de fondo pantalla login: #F7F5F2 --}}
<body class="login-page">
    <div class="position-fixed top-0 end-0 m-3" style="z-index: 1050;">
        <button type="button" id="theme-toggle" class="btn btn-sm btn-ghost btn-theme-toggle" aria-label="Cambiar tema">
            <i id="theme-toggle-icon" class="bi bi-moon-stars-fill"></i>
        </button>
    </div>

    <div class="min-vh-100 d-flex align-items-center justify-content-center">
        <div class="w-100" style="max-width: 400px; padding: 1rem;">

            <div class="text-center mb-4">
                <img src="{{ asset('images/logo-vertical.jpg') }}"
                        alt="Marin Giménez"
                        style="max-width: 200px;">
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    {{ $slot }}
                </div>
            </div>

        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- JS propio --}}
    <script src="{{ asset('js/app-custom.js') }}"></script>
</body>
</html>
