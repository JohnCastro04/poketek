<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ],
        ], [
            // Mensajes para la contraseña actual
            'current_password.required' => 'Por favor, introduce tu contraseña actual para continuar.',
            'current_password.current_password' => 'La contraseña actual introducida no es correcta. Inténtalo de nuevo.',

            // Mensajes para la nueva contraseña
            'password.required' => 'Por favor, introduce una nueva contraseña.',
            'password.confirmed' => 'La confirmación de la nueva contraseña no coincide. Vuelve a introducirla.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La nueva contraseña debe incluir al menos una letra mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).',
        ]);


        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
