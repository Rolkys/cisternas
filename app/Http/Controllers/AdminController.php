<?php

/**
 * DOC: Proyecto Cisternas
 * Archivo personalizado del dominio de negocio.
 * Contiene lógica específica de gestión de cisternas/usuarios/planificación.
 */

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Muestra el listado principal de registros.
     */
    public function index()
    {
        $users = User::orderByDesc('fecha_registro')->get();

        return view('admin.users', compact('users'));
    }

    /**
     * Muestra el formulario para crear un nuevo registro.
     */
    public function create()
    {
        $rolesDisponibles = ['Root', 'Administrador', 'Usuario', 'operario'];

        return view('admin.create', compact('rolesDisponibles'));
    }

    /**
     * Valida la solicitud y crea un nuevo registro.
     */
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
                ->withErrors(['password_generada' => 'Debes generar la contraseña con el botón antes de crear.'])
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
            "Usuario creado correctamente.<br>Contraseña generada: <b>{$plainPassword}</b>"
        );
    }

    /**
     * Activa o desactiva el estado de un registro.
     */
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

    /**
     * Actualiza el rol asignado al usuario indicado.
     */
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

    /**
     * Elimina un registro del sistema.
     */
    public function destroy(User $user)
    {
        if ($user->email === 'root@local.es') {
            return back()->with('error', 'No se puede eliminar el usuario root.');
        }

        $email = $user->email;
        $user->delete();

        return back()->with('success', "Usuario {$email} eliminado correctamente.");
    }

    /**
     * Muestra el formulario para editar un registro.
     */
    public function edit(User $user)
    {
        $rolesDisponibles = ['Root', 'Administrador', 'Usuario', 'operario'];

        return view('admin.edit', compact('user', 'rolesDisponibles'));
    }

    /**
     * Muestra el detalle de un registro concreto.
     */
    public function show(User $user)
    {
        $generatedPassword = $this->generatePasswordFromEmail($user->email);
        $capabilities = $this->getRoleCapabilities($user->role);

        return view('admin.show', compact('user', 'generatedPassword', 'capabilities'));
    }

    /**
     * Valida la solicitud y actualiza un registro existente.
     */
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

    /**
     * Genera una contraseña determinística a partir del email.
     */
    private function generatePasswordFromEmail(string $email): string
    {
        $localPart = trim(explode('@', $email)[0] ?? '');
        if ($localPart === '') {
            throw new \InvalidArgumentException('El email no tiene parte local válida.');
        }

        $upperLocal = strtoupper($localPart);
        $firstChar = $upperLocal[0];
        $lastChar = $upperLocal[strlen($upperLocal) - 1];

        return $upperLocal . ord($firstChar) . ord($lastChar);
    }

    /**
     * Devuelve los permisos visibles asociados a un rol.
     */
    private function getRoleCapabilities(string $role): array
    {
        return match ($role) {
            'Root' => [
                'Gestión total del sistema.',
                'Puede crear, editar y eliminar usuarios.',
                'Puede gestionar todas las cisternas y operaciones.',
            ],
            'Administrador' => [
                'Gestión de usuarios (excepto restricciones del root).',
                'Puede crear, editar y eliminar registros de negocio.',
                'Acceso completo a paneles de administración.',
            ],
            'operario' => [
                'Puede consultar información operativa.',
                'Puede registrar o actualizar datos operativos permitidos.',
                'No puede gestionar usuarios administradores/root.',
            ],
            default => [
                'Puede ver la información permitida por su perfil.',
                'Puede operar sobre funciones básicas habilitadas.',
                'No puede administrar usuarios del sistema.',
            ],
        };
    }
}


