<?php

namespace App\Http\Controllers;

use App\Models\Expensa;
use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GastoController extends Controller
{
    /**
     * Display a listing of admin expenses and financial balance.
     */
    public function index(Request $request)
    {
        $query = Gasto::with('registrador');

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('mes')) {
            $query->whereMonth('fecha', $request->mes);
        }

        if ($request->filled('anio')) {
            $query->whereYear('fecha', $request->anio);
        }

        if ($request->filled('search')) {
            $query->where('descripcion', 'like', "%{$request->search}%");
        }

        $gastos = $query->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        // Financial analytics
        $totalGastos = (float) (clone $query)->sum('monto');
        $totalExpensasRecaudadas = (float) Expensa::where('estado', 'pagado')->sum('monto');
        $balanceGeneral = $totalExpensasRecaudadas - (float) Gasto::sum('monto');

        $categorias = [
            'mantenimiento' => 'Mantenimiento General',
            'servicios' => 'Servicios Básicos (Agua, Luz, Gas)',
            'reparaciones' => 'Reparaciones y Obras',
            'seguridad' => 'Seguridad y Vigilancia',
            'limpieza' => 'Limpieza y Desinfección',
            'otros' => 'Otros Gastos Administrativos',
        ];

        return view('gastos.index', compact(
            'gastos',
            'totalGastos',
            'totalExpensasRecaudadas',
            'balanceGeneral',
            'categorias'
        ));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        $categorias = [
            'mantenimiento' => 'Mantenimiento General',
            'servicios' => 'Servicios Básicos (Agua, Luz, Gas)',
            'reparaciones' => 'Reparaciones y Obras',
            'seguridad' => 'Seguridad y Vigilancia',
            'limpieza' => 'Limpieza y Desinfección',
            'otros' => 'Otros Gastos Administrativos',
        ];

        return view('gastos.create', compact('categorias'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion' => ['required', 'string', 'max:255'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'categoria' => ['required', 'string', 'in:mantenimiento,servicios,reparaciones,seguridad,limpieza,otros'],
            'fecha' => ['required', 'date'],
            'comprobante' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:4096'],
        ], [
            'descripcion.required' => 'La descripción del gasto es obligatoria.',
            'monto.required' => 'El monto es obligatorio.',
            'monto.min' => 'El monto debe ser mayor a 0.',
            'categoria.required' => 'Selecciona una categoría.',
            'fecha.required' => 'La fecha del gasto es obligatoria.',
        ]);

        $data = [
            'descripcion' => $validated['descripcion'],
            'monto' => $validated['monto'],
            'categoria' => $validated['categoria'],
            'fecha' => $validated['fecha'],
            'registrado_por' => Auth::id(),
        ];

        if ($request->hasFile('comprobante')) {
            $data['comprobante'] = $request->file('comprobante')->store('gastos', 'public');
        }

        $gasto = Gasto::create($data);

        return redirect()->route('gastos.index')
            ->with('success', "Gasto '{$gasto->descripcion}' por $ {$gasto->monto} registrado exitosamente.");
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Gasto $gasto)
    {
        $categorias = [
            'mantenimiento' => 'Mantenimiento General',
            'servicios' => 'Servicios Básicos (Agua, Luz, Gas)',
            'reparaciones' => 'Reparaciones y Obras',
            'seguridad' => 'Seguridad y Vigilancia',
            'limpieza' => 'Limpieza y Desinfección',
            'otros' => 'Otros Gastos Administrativos',
        ];

        return view('gastos.edit', compact('gasto', 'categorias'));
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Gasto $gasto)
    {
        $validated = $request->validate([
            'descripcion' => ['required', 'string', 'max:255'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'categoria' => ['required', 'string', 'in:mantenimiento,servicios,reparaciones,seguridad,limpieza,otros'],
            'fecha' => ['required', 'date'],
            'comprobante' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:4096'],
        ]);

        $data = [
            'descripcion' => $validated['descripcion'],
            'monto' => $validated['monto'],
            'categoria' => $validated['categoria'],
            'fecha' => $validated['fecha'],
        ];

        if ($request->hasFile('comprobante')) {
            if ($gasto->comprobante && Storage::disk('public')->exists($gasto->comprobante)) {
                Storage::disk('public')->delete($gasto->comprobante);
            }
            $data['comprobante'] = $request->file('comprobante')->store('gastos', 'public');
        }

        $gasto->update($data);

        return redirect()->route('gastos.index')
            ->with('success', "Gasto actualizado correctamente.");
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Gasto $gasto)
    {
        if ($gasto->comprobante && Storage::disk('public')->exists($gasto->comprobante)) {
            Storage::disk('public')->delete($gasto->comprobante);
        }

        $descripcion = $gasto->descripcion;
        $gasto->delete();

        return redirect()->route('gastos.index')
            ->with('success', "Gasto '{$descripcion}' eliminado con éxito.");
    }
}
