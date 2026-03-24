<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        return view('admin.users', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:Root,Administrador,Usuario,operario',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'is_active' => 1,
            'fecha_registro' => now(),
        ]);

        return redirect()->route('admin.users')->with('success', 'Usuario creado correctamente');
    }

    // 👇 MÉTODO PARA CAMBIAR EL ROL 👇
    public function changeRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:Root,Administrador,Usuario,operario',
        ]);

        $user->role = $request->role;
        $user->save();

        return redirect()->route('admin.users')->with('success', "Rol de {$user->name} actualizado a {$user->role}");
    }

    // 👇 MÉTODO PARA ACTIVAR/DESACTIVAR USUARIO 👇
    public function toggle(User $user)
    {
        // No permitir desactivar al propio usuario
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'No puedes cambiarte el estado a ti mismo');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $estado = $user->is_active ? 'activado' : 'desactivado';
        return redirect()->route('admin.users')->with('success', "Usuario {$user->name} {$estado} correctamente");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'No puedes eliminarte a ti mismo');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Usuario eliminado correctamente');
    }
}