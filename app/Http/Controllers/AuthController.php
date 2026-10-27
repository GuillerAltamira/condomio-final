<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Show login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Por favor introduce un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->activo) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->with('error', 'Tu cuenta se encuentra inactiva. Contacta al administrador.');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', "¡Bienvenido de nuevo, {$user->name}!");
        }

        return back()->withInput($request->only('email', 'remember'))
            ->with('error', 'Las credenciales proporcionadas no coinciden con nuestros registros.');
    }

    /**
     * Show registration form for new resident.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        $departamentos = Departamento::orderBy('bloque')->orderBy('numero_departamento')->get();
        return view('auth.register', compact('departamentos'));
    }

    /**
     * Handle registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'ci' => ['required', 'string', 'max:20', 'unique:users,ci'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Este correo electrónico ya está registrado en el sistema.',
            'ci.required' => 'El carnet de identidad (CI) es obligatorio.',
            'ci.unique' => 'Ya existe un residente registrado con este número de CI.',
            'password.required' => 'La contraseña es requerida.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'ci' => $validated['ci'],
            'telefono' => $validated['telefono'] ?? null,
            'departamento_id' => $validated['departamento_id'] ?? null,
            'password' => Hash::make($validated['password']),
            'rol' => 'residente',
            'activo' => true,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', '¡Registro completado con éxito! Bienvenido a CondominioApp.');
    }

    /**
     * Log out current user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Has cerrado sesión correctamente.');
    }

    /**
     * Show user profile.
     */
    public function profile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    /**
     * Update user profile and avatar.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'ci' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.unique' => 'Este correo ya pertenece a otro usuario.',
            'ci.unique' => 'Este CI ya pertenece a otro usuario.',
            'password.min' => 'La nueva contraseña debe tener mínimo 8 caracteres.',
            'avatar.max' => 'La imagen de perfil no debe superar los 2MB.',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'ci' => $validated['ci'],
            'telefono' => $validated['telefono'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
