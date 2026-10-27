<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Expensa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ExpensaController extends Controller
{
    /**
     * Display a listing of expensas.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Expensa::with(['departamento.propietario']);

        // If resident, filter strictly to their department
        if ($user->isResidente()) {
            if (!$user->departamento_id) {
                return view('expensas.index', [
                    'expensas' => collect(),
                    'totalMonto' => 0,
                    'totalPagado' => 0,
                    'totalPendiente' => 0,
                    'departamentos' => collect(),
                    'sinDepartamento' => true,
                ]);
            }
            $query->where('departamento_id', $user->departamento_id);
        } else {
            // Admin filters
            if ($request->filled('departamento_id')) {
                $query->where('departamento_id', $request->departamento_id);
            }
        }

        // Shared filters: estado, mes, anio
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('mes')) {
            $query->where('mes', $request->mes);
        }

        if ($request->filled('anio')) {
            $query->where('anio', $request->anio);
        }

        // Financial totals based on current filtered query
        $totalesQuery = clone $query;
        $totalMonto = (float) $totalesQuery->sum('monto');
        $totalPagado = (float) (clone $query)->where('estado', 'pagado')->sum('monto');
        $totalPendiente = (float) (clone $query)->whereIn('estado', ['pendiente', 'atrasado'])->sum('monto');

        $expensas = $query->orderByDesc('anio')
            ->orderByDesc('mes')
            ->orderBy('departamento_id')
            ->paginate(15)
            ->withQueryString();

        $departamentos = Departamento::orderBy('bloque')->orderBy('numero_departamento')->get();
        $sinDepartamento = false;

        return view('expensas.index', compact(
            'expensas',
            'totalMonto',
            'totalPagado',
            'totalPendiente',
            'departamentos',
            'sinDepartamento'
        ));
    }

    /**
     * Show form for individual expensa creation.
     */
    public function create()
    {
        $departamentos = Departamento::orderBy('bloque')->orderBy('numero_departamento')->get();
        $mesActual = (int) date('m');
        $anioActual = (int) date('Y');

        return view('expensas.create', compact('departamentos', 'mesActual', 'anioActual'));
    }

    /**
     * Store a single expensa.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'departamento_id' => ['required', 'exists:departamentos,id'],
            'mes' => ['required', 'integer', 'between:1,12'],
            'anio' => ['required', 'integer', 'min:2020', 'max:2035'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'estado' => ['required', 'in:pendiente,pagado,atrasado'],
            'fecha_pago' => ['nullable', 'date'],
            'metodo_pago' => ['nullable', 'string', 'max:50'],
            'notas' => ['nullable', 'string'],
        ], [
            'departamento_id.required' => 'Selecciona un departamento.',
            'mes.between' => 'El mes debe estar entre 1 y 12.',
            'monto.min' => 'El monto debe ser mayor a 0.',
        ]);

        // Duplicate prevention
        $existe = Expensa::where('departamento_id', $validated['departamento_id'])
            ->where('mes', $validated['mes'])
            ->where('anio', $validated['anio'])
            ->exists();

        if ($existe) {
            return back()->withInput()->with('error', 'Ya existe una cuota/expensa registrada para este departamento en el período seleccionado.');
        }

        if ($validated['estado'] === 'pagado' && empty($validated['fecha_pago'])) {
            $validated['fecha_pago'] = now()->toDateString();
        }

        $expensa = Expensa::create($validated);

        return redirect()->route('expensas.index')
            ->with('success', "Expensa de {$expensa->periodo} registrada correctamente.");
    }

    /**
     * Show form for bulk generating monthly expensas for all units.
     */
    public function generarMasivoForm()
    {
        $mesActual = (int) date('m');
        $anioActual = (int) date('Y');
        $totalDeptos = Departamento::count();

        return view('expensas.generar-masivo', compact('mesActual', 'anioActual', 'totalDeptos'));
    }

    /**
     * Bulk generate monthly expensas.
     */
    public function generarMasivo(Request $request)
    {
        $validated = $request->validate([
            'mes' => ['required', 'integer', 'between:1,12'],
            'anio' => ['required', 'integer', 'min:2020', 'max:2035'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'solo_ocupados' => ['nullable', 'boolean'],
        ]);

        $query = Departamento::query();
        if ($request->boolean('solo_ocupados')) {
            $query->where(function ($q) {
                $q->has('residentes')->orWhereNotNull('propietario_id');
            });
        }

        $departamentos = $query->get();
        $creados = 0;
        $omitidos = 0;

        foreach ($departamentos as $depto) {
            $existe = Expensa::where('departamento_id', $depto->id)
                ->where('mes', $validated['mes'])
                ->where('anio', $validated['anio'])
                ->exists();

            if ($existe) {
                $omitidos++;
                continue;
            }

            Expensa::create([
                'departamento_id' => $depto->id,
                'mes' => $validated['mes'],
                'anio' => $validated['anio'],
                'monto' => $validated['monto'],
                'estado' => 'pendiente',
            ]);

            $creados++;
        }

        return redirect()->route('expensas.index')
            ->with('success', "Generación masiva completada: {$creados} expensas creadas con éxito" . ($omitidos > 0 ? " ({$omitidos} ya existían)." : "."));
    }

    /**
     * Record payment and upload proof receipt.
     */
    public function registrarPago(Request $request, Expensa $expensa)
    {
        $user = Auth::user();

        // Residente can only pay their own department's expensas
        if ($user->isResidente() && $expensa->departamento_id !== $user->departamento_id) {
            abort(403, 'No tienes permiso para pagar esta expensa.');
        }

        $validated = $request->validate([
            'fecha_pago' => ['required', 'date'],
            'metodo_pago' => ['required', 'string', 'in:transferencia,qr,efectivo,tarjeta,deposito'],
            'comprobante_imagen' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,pdf,JPG,JPEG,PNG', 'max:4096'],
            'notas' => ['nullable', 'string', 'max:500'],
        ], [
            'fecha_pago.required' => 'La fecha de pago es obligatoria.',
            'metodo_pago.required' => 'Selecciona un método de pago.',
            'comprobante_imagen.max' => 'El comprobante no debe superar los 4MB.',
        ]);

        $data = [
            'estado' => 'pagado',
            'fecha_pago' => $validated['fecha_pago'],
            'metodo_pago' => $validated['metodo_pago'],
            'notas' => $validated['notas'] ?? $expensa->notas,
        ];

        if ($request->hasFile('comprobante_imagen')) {
            // Borrar el comprobante anterior si existe físicamente en la carpeta pública
            if ($expensa->comprobante_imagen) {
                $cleanOldPath = str_replace(['public/', 'storage/'], '', $expensa->comprobante_imagen);
                $oldPath = base_path('public/storage/' . $cleanOldPath);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file = $request->file('comprobante_imagen');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Ruta física exacta en htdocs/public/storage/comprobantes
            $destinationPath = base_path('public/storage/comprobantes');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $data['comprobante_imagen'] = 'comprobantes/' . $filename;
        }

        $expensa->update($data);

        return back()->with('success', "Pago de expensa ({$expensa->periodo}) registrado exitosamente.");
    }

    /**
     * Quick status change (for admin).
     */
    public function cambiarEstado(Request $request, Expensa $expensa)
    {
        $request->validate([
            'estado' => ['required', 'in:pendiente,pagado,atrasado'],
        ]);

        $nuevoEstado = $request->estado;
        $data = ['estado' => $nuevoEstado];

        if ($nuevoEstado === 'pagado' && !$expensa->fecha_pago) {
            $data['fecha_pago'] = now()->toDateString();
        }

        $expensa->update($data);

        return back()->with('success', "Estado de la expensa actualizado a " . strtoupper($nuevoEstado) . ".");
    }

    /**
     * Delete an expensa record.
     */
    public function destroy(Expensa $expensa)
    {
        if ($expensa->comprobante_imagen) {
            $cleanOldPath = str_replace(['public/', 'storage/'], '', $expensa->comprobante_imagen);
            $imagePath = base_path('public/storage/' . $cleanOldPath);
            if (file_exists($imagePath)) {
                @unlink($imagePath);
            }
        }

        $periodo = $expensa->periodo;
        $expensa->delete();

        return redirect()->route('expensas.index')
            ->with('success', "Expensa ({$periodo}) eliminada con éxito.");
    }
}