<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ResidenteController extends Controller
{
    /**
     * Display a listing of residents / owners.
     */
    public function index(Request $request)
    {
        $query = User::with(['departamento', 'departamentosPropiedad'])
            ->where('rol', 'residente');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('ci', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        if ($request->filled('departamento_id')) {
            $query->where('departamento_id', $request->departamento_id);
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        $residentes = $query->orderBy('name')->paginate(10)->withQueryString();
        $departamentos = Departamento::orderBy('bloque')->orderBy('numero_departamento')->get();

        return view('residentes.index', compact('residentes', 'departamentos'));
    }

    /**
     * Show the form for creating a new resident.
     */
    public function create()
    {
        $departamentos = Departamento::orderBy('bloque')->orderBy('numero_departamento')->get();
        return view('residentes.create', compact('departamentos'));
    }

    /**
     * Store a newly created resident in storage with strict uniqueness validation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'ci' => ['required', 'string', 'max:20', 'unique:users,ci'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
            'password' => ['nullable', 'string', 'min:6'],
            'es_propietario' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'El nombre completo del residente es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Ya existe un usuario o residente registrado con este correo electrónico.',
            'ci.required' => 'El Carnet de Identidad (CI) es obligatorio.',
            'ci.unique' => 'Ya existe un residente registrado con este número de Carnet de Identidad (CI).',
            'departamento_id.exists' => 'El departamento seleccionado no es válido.',
        ]);

        $password = !empty($validated['password']) ? $validated['password'] : 'residente123';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'ci' => $validated['ci'],
            'telefono' => $validated['telefono'] ?? null,
            'departamento_id' => $validated['departamento_id'] ?? null,
            'password' => Hash::make($password),
            'rol' => 'residente',
            'activo' => true,
        ]);

        // If marked as propietario and department is selected, update owner
        if (!empty($validated['departamento_id']) && $request->boolean('es_propietario')) {
            Departamento::where('id', $validated['departamento_id'])->update(['propietario_id' => $user->id]);
        }

        return redirect()->route('residentes.index')
            ->with('success', "Residente {$user->name} registrado exitosamente con CI {$user->ci}. Contraseña inicial: {$password}");
    }

    /**
     * Display the specified resident details.
     */
    public function show(User $residente)
    {
        $residente->load([
            'departamento.propietario',
            'departamentosPropiedad',
            'reservas.areaComun',
        ]);

        $expensas = collect();
        if ($residente->departamento_id) {
            $expensas = $residente->departamento->expensas()->orderByDesc('anio')->orderByDesc('mes')->get();
        }

        return view('residentes.show', compact('residente', 'expensas'));
    }

    /**
     * Show the form for editing the specified resident.
     */
    public function edit(User $residente)
    {
        $departamentos = Departamento::orderBy('bloque')->orderBy('numero_departamento')->get();
        return view('residentes.edit', compact('residente', 'departamentos'));
    }

    /**
     * Update the specified resident with strict uniqueness.
     */
    public function update(Request $request, User $residente)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($residente->id)],
            'ci' => ['required', 'string', 'max:20', Rule::unique('users')->ignore($residente->id)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
            'password' => ['nullable', 'string', 'min:6'],
            'activo' => ['required', 'boolean'],
            'es_propietario' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'El nombre completo es obligatorio.',
            'email.unique' => 'Ya existe otro usuario registrado con este correo electrónico.',
            'ci.unique' => 'Ya existe otro usuario registrado con este Carnet de Identidad (CI).',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'ci' => $validated['ci'],
            'telefono' => $validated['telefono'] ?? null,
            'departamento_id' => $validated['departamento_id'] ?? null,
            'activo' => (bool) $validated['activo'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $residente->update($data);

        if (!empty($validated['departamento_id']) && $request->boolean('es_propietario')) {
            Departamento::where('id', $validated['departamento_id'])->update(['propietario_id' => $residente->id]);
        }

        return redirect()->route('residentes.index')
            ->with('success', "Datos del residente {$residente->name} actualizados correctamente.");
    }

    /**
     * Remove or toggle status of the resident.
     */
    public function destroy(User $residente)
    {
        // Don't delete if they have pending reservations
        if ($residente->reservas()->where('estado', 'confirmada')->where('fecha_reserva', '>=', now())->exists()) {
            return back()->with('error', 'El residente tiene reservas activas futuras. Cancela las reservas antes de eliminar.');
        }

        $nombre = $residente->name;
        $residente->delete();

        return redirect()->route('residentes.index')
            ->with('success', "El residente {$nombre} ha sido eliminado del sistema.");
    }
}
