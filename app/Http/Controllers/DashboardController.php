<?php

namespace App\Http\Controllers;

use App\Models\AreaComun;
use App\Models\Departamento;
use App\Models\Expensa;
use App\Models\Gasto;
use App\Models\Reserva;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the main system dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->residenteDashboard($user);
    }

    /**
     * Dashboard for Administrators.
     */
    protected function adminDashboard()
    {
        $mesActual = (int) date('m');
        $anioActual = (int) date('Y');

        // Department Metrics
        $totalDeptos = Departamento::count();
        $deptosOcupados = Departamento::has('residentes')->orWhereNotNull('propietario_id')->count();
        $deptosLibres = max(0, $totalDeptos - $deptosOcupados);

        // Resident Metrics
        $totalResidentes = User::where('rol', 'residente')->where('activo', true)->count();

        // Expensas Metrics (Current Month)
        $expensasMes = Expensa::where('mes', $mesActual)->where('anio', $anioActual);
        $totalFacturadoMes = (float) $expensasMes->sum('monto');
        $totalRecaudadoMes = (float) Expensa::where('mes', $mesActual)
            ->where('anio', $anioActual)
            ->where('estado', 'pagado')
            ->sum('monto');
        $totalPendienteMes = (float) Expensa::where('mes', $mesActual)
            ->where('anio', $anioActual)
            ->whereIn('estado', ['pendiente', 'atrasado'])
            ->sum('monto');

        // Total Gastos Mes
        $totalGastosMes = (float) Gasto::whereMonth('fecha', $mesActual)
            ->whereYear('fecha', $anioActual)
            ->sum('monto');

        // Total Historical Cash Flow
        $totalIngresosHistorico = (float) Expensa::where('estado', 'pagado')->sum('monto');
        $totalGastosHistorico = (float) Gasto::sum('monto');
        $balanceGeneral = $totalIngresosHistorico - $totalGastosHistorico;

        // Upcoming Reservations with Eager Loading (prevent N+1)
        $proximasReservas = Reserva::with(['areaComun', 'residente.departamento'])
            ->where('fecha_reserva', '>=', now()->toDateString())
            ->where('estado', 'confirmada')
            ->orderBy('fecha_reserva')
            ->orderBy('hora_inicio')
            ->limit(5)
            ->get();

        // Recent Payments with Eager Loading
        $ultimosPagos = Expensa::with(['departamento.propietario'])
            ->where('estado', 'pagado')
            ->whereNotNull('fecha_pago')
            ->orderByDesc('fecha_pago')
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        // Chart Data: Last 6 Months (Income vs Expense)
        $chartLabels = [];
        $chartIngresos = [];
        $chartGastos = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $m = (int) $date->format('m');
            $y = (int) $date->format('Y');

            $chartLabels[] = $date->translatedFormat('M Y');
            $chartIngresos[] = (float) Expensa::where('mes', $m)->where('anio', $y)->where('estado', 'pagado')->sum('monto');
            $chartGastos[] = (float) Gasto::whereMonth('fecha', $m)->whereYear('fecha', $y)->sum('monto');
        }

        return view('dashboard.admin', compact(
            'totalDeptos',
            'deptosOcupados',
            'deptosLibres',
            'totalResidentes',
            'totalFacturadoMes',
            'totalRecaudadoMes',
            'totalPendienteMes',
            'totalGastosMes',
            'balanceGeneral',
            'proximasReservas',
            'ultimosPagos',
            'chartLabels',
            'chartIngresos',
            'chartGastos'
        ));
    }

    /**
     * Dashboard for Residents.
     */
    protected function residenteDashboard(User $user)
    {
        // Eager load resident department, owner, and co-residents
        $user->load(['departamento.propietario', 'departamento.residentes']);
        $departamento = $user->departamento;

        $expensasPendientes = collect();
        $totalDeuda = 0.00;
        $historialExpensas = collect();

        if ($departamento) {
            $expensasPendientes = Expensa::where('departamento_id', $departamento->id)
                ->whereIn('estado', ['pendiente', 'atrasado'])
                ->orderBy('anio', 'desc')
                ->orderBy('mes', 'desc')
                ->get();

            $totalDeuda = (float) $expensasPendientes->sum('monto');

            $historialExpensas = Expensa::where('departamento_id', $departamento->id)
                ->orderBy('anio', 'desc')
                ->orderBy('mes', 'desc')
                ->limit(6)
                ->get();
        }

        // Resident's upcoming reservations with Eager Loading
        $misReservas = Reserva::with('areaComun')
            ->where('residente_id', $user->id)
            ->where('fecha_reserva', '>=', now()->toDateString())
            ->where('estado', '!=', 'cancelada')
            ->orderBy('fecha_reserva')
            ->orderBy('hora_inicio')
            ->limit(5)
            ->get();

        // Available Common Areas
        $areasComunes = AreaComun::where('activo', true)->orderBy('nombre')->get();

        return view('dashboard.residente', compact(
            'user',
            'departamento',
            'expensasPendientes',
            'totalDeuda',
            'historialExpensas',
            'misReservas',
            'areasComunes'
        ));
    }
}
