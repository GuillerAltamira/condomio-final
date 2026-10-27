<?php

namespace App\Http\Controllers;

use App\Models\AreaComun;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{
    /**
     * Display a listing of reservations.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Reserva::with(['areaComun', 'residente.departamento']);

        // Resident only sees their own reservations
        if ($user->isResidente()) {
            $query->where('residente_id', $user->id);
        } else {
            // Admin filter by resident
            if ($request->filled('residente_id')) {
                $query->where('residente_id', $request->residente_id);
            }
        }

        // Shared filters
        if ($request->filled('area_comun_id')) {
            $query->where('area_comun_id', $request->area_comun_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha_reserva', $request->fecha);
        }

        $reservas = $query->orderByDesc('fecha_reserva')
            ->orderBy('hora_inicio')
            ->paginate(12)
            ->withQueryString();

        $areas = AreaComun::where('activo', true)->orderBy('nombre')->get();
        $residentes = User::where('rol', 'residente')->where('activo', true)->orderBy('name')->get();

        return view('reservas.index', compact('reservas', 'areas', 'residentes'));
    }

    /**
     * Show the form for creating a new reservation.
     */
    public function create(Request $request)
    {
        $areas = AreaComun::where('activo', true)->orderBy('nombre')->get();
        $residentes = User::where('rol', 'residente')->where('activo', true)->orderBy('name')->get();
        $areaSeleccionadaId = $request->get('area_id');

        return view('reservas.create', compact('areas', 'residentes', 'areaSeleccionadaId'));
    }

    /**
     * Store a newly created reservation with strict anti-overlap validation.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'area_comun_id' => ['required', 'exists:areas_comunes,id'],
            'fecha_reserva' => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'motivo' => ['nullable', 'string', 'max:500'],
        ];

        // If admin, they can reserve on behalf of any resident
        if ($user->isAdmin()) {
            $rules['residente_id'] = ['required', 'exists:users,id'];
        }

        $validated = $request->validate($rules, [
            'area_comun_id.required' => 'Debes seleccionar un área común.',
            'fecha_reserva.after_or_equal' => 'La fecha de reserva no puede ser en el pasado.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_fin.required' => 'La hora de finalización es obligatoria.',
            'hora_fin.after' => 'La hora de finalización debe ser posterior a la hora de inicio.',
            'residente_id.required' => 'Selecciona el residente.',
        ]);

        $residenteId = $user->isAdmin() ? $validated['residente_id'] : $user->id;

        // Anti-overlap validation check
        $solapado = Reserva::existeSolapamiento(
            $validated['area_comun_id'],
            $validated['fecha_reserva'],
            $validated['hora_inicio'],
            $validated['hora_fin']
        );

        if ($solapado) {
            return back()->withInput()->with('error', '¡Conflicto de horario! El área seleccionada ya tiene una reserva confirmada que se solapa con el horario elegido. Por favor elige otro horario o fecha.');
        }

        $reserva = Reserva::create([
            'area_comun_id' => $validated['area_comun_id'],
            'residente_id' => $residenteId,
            'fecha_reserva' => $validated['fecha_reserva'],
            'hora_inicio' => $validated['hora_inicio'],
            'hora_fin' => $validated['hora_fin'],
            'estado' => 'confirmada',
            'motivo' => $validated['motivo'] ?? null,
        ]);

        return redirect()->route('reservas.index')
            ->with('success', "¡Reserva confirmada con éxito para el {$reserva->fecha_reserva->format('d/m/Y')} de {$reserva->horario_formateado}!");
    }

    /**
     * Cancel reservation.
     */
    public function cancelar(Reserva $reserva)
    {
        $user = Auth::user();

        // Resident can only cancel their own reservation
        if ($user->isResidente() && $reserva->residente_id !== $user->id) {
            abort(403, 'No tienes permiso para cancelar esta reserva.');
        }

        $reserva->update(['estado' => 'cancelada']);

        return back()->with('info', 'La reserva ha sido cancelada.');
    }

    /**
     * Remove reservation from database (admin only).
     */
    public function destroy(Reserva $reserva)
    {
        $reserva->delete();

        return redirect()->route('reservas.index')
            ->with('success', 'Reserva eliminada del historial.');
    }
}
