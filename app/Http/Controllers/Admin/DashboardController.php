<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // ¡Importa el modelo User!

class DashboardController extends Controller
{
    public function index()
    {
        // VERIFICACIÓN DEL PERMISO AQUÍ (asegúrate de que esta lógica funciona)
        if (!Auth::check() || !Auth::user()->permission) {
            abort(403, 'Acceso no autorizado. No tienes permisos de administrador.');
        }

        // Obtener todos los usuarios, excepto el usuario actualmente autenticado (opcional)
        // O si quieres incluir al admin, simplemente quita el 'where'
        $users = User::where('id', '!=', Auth::id())->get(); // Excluye al propio admin de la lista para evitar que se borre a sí mismo accidentalmente.

        // O para incluir al admin:
        // $users = User::all();

        return view('admin.dashboard', compact('users')); // Pasa los usuarios a la vista
    }

    // Método para mostrar el formulario de edición de un usuario
    public function editUser(User $user)
    {
        // VERIFICACIÓN DEL PERMISO (de nuevo para mayor seguridad en la ruta)
        if (!Auth::check() || !Auth::user()->permission) {
            abort(403, 'Acceso no autorizado. No tienes permisos de administrador.');
        }

        return view('admin.users.edit', compact('user'));
    }

    // Método para actualizar los datos de un usuario
    public function updateUser(Request $request, User $user)
    {
        // VERIFICACIÓN DEL PERMISO (importante mantenerla)
        if (!Auth::check() || !Auth::user()->permission) {
            abort(403, 'Acceso no autorizado. No tienes permisos de administrador.');
        }

        // Validación de los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'max:255',
                'email',
                'regex:/^.+@.+\..+$/i',
                'unique:users,email,' . $user->id,
            ],
            'permission' => 'required|boolean',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'permission' => $request->permission,
        ]);

        return redirect()->route('admin.dashboard')->with('status', 'Usuario actualizado correctamente.');
    }

    // Método para eliminar un usuario
    public function deleteUser(User $user)
    {
        // VERIFICACIÓN DEL PERMISO
        if (!Auth::check() || !Auth::user()->permission) {
            abort(403, 'Acceso no autorizado. No tienes permisos de administrador.');
        }

        // Evitar que un administrador se elimine a sí mismo
        if (Auth::id() === $user->id) {
            return redirect()->route('admin.dashboard')->with('error', 'No puedes eliminar tu propia cuenta de administrador.');
        }

        $user->delete();

        return redirect()->route('admin.dashboard')->with('status', 'Usuario eliminado correctamente.');
    }
}
