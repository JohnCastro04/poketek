<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:25'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ],
        ], [
            // Mensajes de validación para el nombre
            'name.required' => 'Por favor, introduce tu nombre completo.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no puede superar los 25 caracteres.',

            // Mensajes de validación para el correo electrónico
            'email.required' => 'Por favor, introduce tu dirección de correo electrónico.',
            'email.string' => 'El correo electrónico debe tener un formato válido.',
            'email.email' => 'Introduce una dirección de correo electrónico válida.',
            'email.max' => 'El correo electrónico no puede superar los 255 caracteres.',
            'email.unique' => 'Este correo ya está registrado. Usa uno diferente o inicia sesión.',

            // Mensajes de validación para la contraseña
            'password.required' => 'Por favor, crea una contraseña para tu cuenta.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide. Vuelve a escribirla.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe incluir al menos una letra mayúscula, una minúscula, un número y un carácter especial (@$!%*?&).',
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_picture' => rand(1, 20), // Asignar foto aleatoria
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('home'));
    }
}
