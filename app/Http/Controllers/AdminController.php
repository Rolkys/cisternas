<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ==================== INDEX ====================
    public function index()
    {
        $users = User::orderBy('role')->orderBy('email')->get();
        return view('admin.users', compact('users'));
    }

    // ==================== STORE ====================
    public function store(Request $request)
    {
        $request->validate([
            'email'             => 'required|email|unique:users,email',
            'password_generada' => 'required|string',
            'role'              => 'required|in:Root,Administrador,Usuario',
        ]);

        if (in_array($request->role, ['Root', 'Administrador']) && auth()->user()->role !== 'Root') {
            abort(403, 'Solo Root puede crear administradores.');
        }

        $password = $request->password_generada;
        $name     = ucfirst(strtolower(explode('@', $request->email)[0]));

        User::create([
            'name'           => $name,
            'email'          => $request->email,
            'password'       => Hash::make($password),
            'role'           => $request->role,
            'is_active'      => true,
            'fecha_registro' => now(),
        ]);

        return redirect()->route('admin.users')
                         ->with('success', "✅ Usuario creado. Contraseña: {$password}");
    }

    // ==================== TOGGLE ACTIVO ====================
    public function toggle(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', '❌ No puedes desactivarte a ti mismo.');
        }

        if (in_array($user->role, ['Root', 'Administrador']) && auth()->user()->role !== 'Root') {
            abort(403);
        }

        $user->update(['is_active' => !$user->is_active]);

        $estado = $user->is_active ? 'activado' : 'desactivado';
        return redirect()->back()->with('success', "✅ Usuario $estado correctamente.");
    }

    // ==================== CAMBIAR ROL ====================
    public function changeRole(Request $request, User $user)
    {
        if (auth()->user()->role !== 'Root') {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', '❌ No puedes cambiar tu propio rol.');
        }

        $request->validate([
            'role' => 'required|in:Root,Administrador,Usuario',
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->back()->with('success', '✅ Rol actualizado correctamente.');
    }

    // ==================== DESTROY ====================
    public function destroy(User $user)
    {
        if (auth()->user()->role !== 'Root') {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', '❌ No puedes eliminarte a ti mismo.');
        }

        $user->delete();

        return redirect()->back()->with('success', '✅ Usuario eliminado correctamente.');
    }
}