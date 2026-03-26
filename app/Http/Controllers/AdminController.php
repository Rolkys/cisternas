<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('fecha_registro')->get();

        return view('admin.users', compact('users'));
    }

    public function create()
    {
        $rolesDisponibles = ['Root', 'Administrador', 'Usuario', 'operario'];

        return view('admin.create', compact('rolesDisponibles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password_generada' => 'required|string|min:3',
            'role' => ['required', Rule::in(['Root', 'Administrador', 'Usuario', 'operario'])],
        ]);

        $plainPassword = $this->generatePasswordFromEmail($validated['email']);
        if ($validated['password_generada'] !== $plainPassword) {
            return back()
                ->withErrors(['password_generada' => 'Debes generar la contrasena con el boton antes de crear.'])
                ->withInput();
        }

        User::create([
            'name' => explode('@', $validated['email'])[0],
            'email' => $validated['email'],
            'password' => Hash::make($plainPassword),
            'role' => $validated['role'],
            'is_active' => true,
            'fecha_registro' => now(),
        ]);

        Log::info('Usuario creado por administrador', [
            'user_email' => $validated['email'],
            'admin_email' => auth()->user()?->email,
        ]);

        return redirect()->route('admin.users')->with(
            'success',
            "Usuario creado correctamente.<br>Contrasena generada: <b>{$plainPassword}</b>"
        );
    }

    public function toggle(User $user)
    {
        if ($user->email === 'root@local.es') {
            return back()->with('error', 'No se puede modificar el estado del usuario root.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $message = $user->is_active
            ? 'Usuario activado correctamente.'
            : 'Usuario desactivado correctamente.';

        return back()->with('success', $message);
    }

    public function changeRole(Request $request, User $user)
    {
        if ($user->email === 'root@local.es') {
            return back()->with('error', 'No se puede modificar el rol del usuario root.');
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(['Root', 'Administrador', 'Usuario', 'operario'])],
        ]);

        $user->role = $validated['role'];
        $user->save();

        return back()->with('success', "Rol actualizado a {$validated['role']} para {$user->email}.");
    }

    public function destroy(User $user)
    {
        if ($user->email === 'root@local.es') {
            return back()->with('error', 'No se puede eliminar el usuario root.');
        }

        $email = $user->email;
        $user->delete();

        return back()->with('success', "Usuario {$email} eliminado correctamente.");
    }

    public function edit(User $user)
    {
        $rolesDisponibles = ['Root', 'Administrador', 'Usuario', 'operario'];

        return view('admin.edit', compact('user', 'rolesDisponibles'));
    }

    public function show(User $user)
    {
        $generatedPassword = $this->generatePasswordFromEmail($user->email);
        $capabilities = $this->getRoleCapabilities($user->role);

        return view('admin.show', compact('user', 'generatedPassword', 'capabilities'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['Root', 'Administrador', 'Usuario', 'operario'])],
            'is_active' => 'nullable|boolean',
        ]);

        $user->role = $validated['role'];
        $user->is_active = $request->boolean('is_active');
        $user->save();

        return redirect()->route('admin.users')->with('success', "Usuario {$user->email} actualizado correctamente.");
    }

    private function generatePasswordFromEmail(string $email): string
    {
        $localPart = trim(explode('@', $email)[0] ?? '');
        if ($localPart === '') {
            throw new \InvalidArgumentException('El email no tiene parte local valida.');
        }

        $upperLocal = strtoupper($localPart);
        $firstChar = $upperLocal[0];
        $lastChar = $upperLocal[strlen($upperLocal) - 1];

        return $upperLocal . ord($firstChar) . ord($lastChar);
    }

    private function getRoleCapabilities(string $role): array
    {
        return match ($role) {
            'Root' => [
                'Gestion total del sistema.',
                'Puede crear, editar y eliminar usuarios.',
                'Puede gestionar todas las cisternas y operaciones.',
            ],
            'Administrador' => [
                'Gestion de usuarios (excepto restricciones del root).',
                'Puede crear, editar y eliminar registros de negocio.',
                'Acceso completo a paneles de administracion.',
            ],
            'operario' => [
                'Puede consultar informacion operativa.',
                'Puede registrar o actualizar datos operativos permitidos.',
                'No puede gestionar usuarios administradores/root.',
            ],
            default => [
                'Puede ver la informacion permitida por su perfil.',
                'Puede operar sobre funciones basicas habilitadas.',
                'No puede administrar usuarios del sistema.',
            ],
        };
    }
}
