<x-guest-layout>
    <h5 class="mb-4 text-center">Iniciar sesión</h5>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                value="{{ old('email') }}" required autofocus>
        </div>

        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <div class="input-group">
                <input type="password" name="password" class="form-control" id="password" required>
                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                    <i class="bi bi-eye" id="eye-icon"></i>
                </button>
            </div>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">Recordarme</label>
        </div>

        <button type="submit" class="btn btn-primary w-100">Entrar</button>
    </form>
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
</script>
</x-guest-layout>