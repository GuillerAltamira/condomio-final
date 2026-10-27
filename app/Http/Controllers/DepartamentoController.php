<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Departamento::with(['propietario', 'residentes'])
            ->withCount(['expensas', 'residentes']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_departamento', 'like', "%{$search}%")
                  ->orWhere('bloque', 'like', "%{$search}%")
                  ->orWhereHas('propietario', function ($ownerQ) use ($search) {
                      $ownerQ->where('name', 'like', "%{$search}%")
                             ->orWhere('ci', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by block
        if ($request->filled('bloque')) {
            $query->where('bloque', $request->bloque);
        }

        // Filter by floor
        if ($request->filled('piso')) {
            $query->where('piso', $request->piso);
        }

        $departamentos = $query->orderBy('bloque')->orderBy('piso')->orderBy('numero_departamento')->paginate(12)->withQueryString();

        $bloques = Departamento::select('bloque')->distinct()->whereNotNull('bloque')->pluck('bloque');
        $pisos = Departamento::select('piso')->distinct()->orderBy('piso')->pluck('piso');

        return view('departamentos.index', compact('departamentos', 'bloques', 'pisos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $propietarios = User::where('activo', true)->orderBy('name')->get();
        return view('departamentos.create', compact('propietarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_departamento' => [
                'required',
                'string',
                'max:20',
                Rule::unique('departamentos', 'numero_departamento')->where(function ($query) use ($request) {
                    return $query->where('bloque', $request->bloque);
                }),
            ],
            'piso' => ['required', 'integer', 'min:0', 'max:100'],
            'bloque' => ['nullable', 'string', 'max:50'],
            'propietario_id' => ['nullable', 'exists:users,id'],
        ], [
            'numero_departamento.required' => 'El número de departamento es obligatorio.',
            'numero_departamento.unique' => 'Ya existe un departamento con este número en el mismo bloque.',
            'piso.required' => 'El número de piso es obligatorio.',
            'propietario_id.exists' => 'El propietario seleccionado no es válido.',
        ]);

        $departamento = Departamento::create($validated);

        // If owner is selected, also link the owner's resident unit if not set
        if (!empty($validated['propietario_id'])) {
            $owner = User::find($validated['propietario_id']);
            if ($owner && !$owner->departamento_id) {
                $owner->update(['departamento_id' => $departamento->id]);
            }
        }

        return redirect()->route('departamentos.index')
            ->with('success', "Departamento {$departamento->identificador_completo} creado exitosamente.");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Departamento $departamento)
    {
        $departamento->load(['propietario', 'residentes']);
        $propietarios = User::where('activo', true)->orderBy('name')->get();
        return view('departamentos.edit', compact('departamento', 'propietarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Departamento $departamento)
    {
        $validated = $request->validate([
            'numero_departamento' => [
                'required',
                'string',
                'max:20',
                Rule::unique('departamentos', 'numero_departamento')
                    ->where(function ($query) use ($request) {
                        return $query->where('bloque', $request->bloque);
                    })
                    ->ignore($departamento->id),
            ],
            'piso' => ['required', 'integer', 'min:0', 'max:100'],
            'bloque' => ['nullable', 'string', 'max:50'],
            'propietario_id' => ['nullable', 'exists:users,id'],
        ], [
            'numero_departamento.required' => 'El número de departamento es obligatorio.',
            'numero_departamento.unique' => 'Ya existe un departamento con este número en el mismo bloque.',
            'piso.required' => 'El número de piso es obligatorio.',
        ]);

        $departamento->update($validated);

        return redirect()->route('departamentos.index')
            ->with('success', "Departamento actualizado con éxito.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Departamento $departamento)
    {
        // Integrity check: prevent deletion if active expensas or residents exist
        if ($departamento->expensas()->exists()) {
            return back()->with('error', 'No se puede eliminar este departamento porque tiene historial de expensas registrado.');
        }

        if ($departamento->residentes()->exists()) {
            return back()->with('error', 'No se puede eliminar este departamento porque tiene residentes asignados.');
        }

        $identificador = $departamento->identificador_completo;
        $departamento->delete();

        return redirect()->route('departamentos.index')
            ->with('success', "Departamento {$identificador} eliminado correctamente.");
    }
}
