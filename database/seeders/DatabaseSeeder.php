<?php

namespace Database\Seeders;

use App\Models\AreaComun;
use App\Models\Departamento;
use App\Models\Expensa;
use App\Models\Gasto;
use App\Models\Reserva;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with complete realistic condominium data.
     */
    public function run(): void
    {
        // 1. Create Administrator
        $admin = User::create([
            'name' => 'Administrador General',
            'email' => 'admin@condominio.com',
            'ci' => '1000001',
            'telefono' => '70000001',
            'rol' => 'admin',
            'activo' => true,
            'password' => Hash::make('admin123'),
        ]);

        // 2. Create Resident Users
        $residentesData = [
            ['name' => 'Carlos Mendoza', 'email' => 'carlos@condominio.com', 'ci' => '7890123', 'telefono' => '71234567'],
            ['name' => 'María Rodríguez', 'email' => 'maria@condominio.com', 'ci' => '6543210', 'telefono' => '72345678'],
            ['name' => 'Roberto Gómez', 'email' => 'roberto@condominio.com', 'ci' => '5432109', 'telefono' => '73456789'],
            ['name' => 'Ana Laura Silva', 'email' => 'ana@condominio.com', 'ci' => '4321098', 'telefono' => '74567890'],
            ['name' => 'Javier Morales', 'email' => 'javier@condominio.com', 'ci' => '3210987', 'telefono' => '75678901'],
            ['name' => 'Patricia Fernández', 'email' => 'patricia@condominio.com', 'ci' => '2109876', 'telefono' => '76789012'],
            ['name' => 'Fernando Castro', 'email' => 'fernando@condominio.com', 'ci' => '1987654', 'telefono' => '77890123'],
            ['name' => 'Lucía Vargas', 'email' => 'lucia@condominio.com', 'ci' => '8765432', 'telefono' => '78901234'],
            ['name' => 'Diego Paredes', 'email' => 'diego@condominio.com', 'ci' => '9876543', 'telefono' => '79012345'],
            ['name' => 'Valentina Ríos', 'email' => 'valentina@condominio.com', 'ci' => '8901234', 'telefono' => '70123456'],
        ];

        $users = [];
        foreach ($residentesData as $res) {
            $users[] = User::create([
                'name' => $res['name'],
                'email' => $res['email'],
                'ci' => $res['ci'],
                'telefono' => $res['telefono'],
                'rol' => 'residente',
                'activo' => true,
                'password' => Hash::make('residente123'),
            ]);
        }

        // 3. Create Departamentos (Torre A and Torre B)
        $deptosConfig = [
            ['numero' => '101', 'piso' => 1, 'bloque' => 'A', 'ownerIndex' => 0],
            ['numero' => '102', 'piso' => 1, 'bloque' => 'A', 'ownerIndex' => 1],
            ['numero' => '201', 'piso' => 2, 'bloque' => 'A', 'ownerIndex' => 2],
            ['numero' => '202', 'piso' => 2, 'bloque' => 'A', 'ownerIndex' => 3],
            ['numero' => '301', 'piso' => 3, 'bloque' => 'A', 'ownerIndex' => 4],
            ['numero' => '302', 'piso' => 3, 'bloque' => 'A', 'ownerIndex' => 5],
            ['numero' => '101', 'piso' => 1, 'bloque' => 'B', 'ownerIndex' => 6],
            ['numero' => '102', 'piso' => 1, 'bloque' => 'B', 'ownerIndex' => 7],
            ['numero' => '201', 'piso' => 2, 'bloque' => 'B', 'ownerIndex' => 8],
            ['numero' => '202', 'piso' => 2, 'bloque' => 'B', 'ownerIndex' => 9],
            ['numero' => '301', 'piso' => 3, 'bloque' => 'B', 'ownerIndex' => null], // Vacant
            ['numero' => '302', 'piso' => 3, 'bloque' => 'B', 'ownerIndex' => null], // Vacant
        ];

        $departamentos = [];
        foreach ($deptosConfig as $dc) {
            $ownerId = ($dc['ownerIndex'] !== null && isset($users[$dc['ownerIndex']])) ? $users[$dc['ownerIndex']]->id : null;
            $depto = Departamento::create([
                'numero_departamento' => $dc['numero'],
                'piso' => $dc['piso'],
                'bloque' => $dc['bloque'],
                'propietario_id' => $ownerId,
            ]);
            $departamentos[] = $depto;

            // Link resident to this department
            if ($ownerId !== null) {
                $users[$dc['ownerIndex']]->update(['departamento_id' => $depto->id]);
            }
        }

        // 4. Create Áreas Comunes
        $areas = [
            AreaComun::create([
                'nombre' => 'Piscina Olímpica y Solarium',
                'descripcion' => 'Piscina al aire libre con control de temperatura, reposeras, sombrillas y área para niños.',
                'imagen' => 'areas_comunes/piscina.jpg',
                'capacidad' => 45,
                'costo_reserva' => 0.00,
                'normas' => 'Uso obligatorio de traje de baño. Prohibido el ingreso de envases de vidrio y mascotas.',
                'activo' => true,
            ]),
            AreaComun::create([
                'nombre' => 'Salón Social de Eventos',
                'descripcion' => 'Salón climatizado para celebraciones, equipamiento audiovisual, mesas, sillas y cocina de apoyo.',
                'imagen' => 'areas_comunes/salon.jpg',
                'capacidad' => 100,
                'costo_reserva' => 50.00,
                'normas' => 'Música a volumen moderado hasta las 02:00 AM. Dejar el salón limpio tras su uso.',
                'activo' => true,
            ]),
            AreaComun::create([
                'nombre' => 'Quincho y Parrillero Premium',
                'descripcion' => 'Espacio techado con asador de acero inoxidable, barra americana, vajilla y refrigerador.',
                'imagen' => 'areas_comunes/quincho.jpg',
                'capacidad' => 30,
                'costo_reserva' => 20.00,
                'normas' => 'Apagar brasas completamente al retirarse y depositar residuos en los contenedores designados.',
                'activo' => true,
            ]),
            AreaComun::create([
                'nombre' => 'Gimnasio Equipado',
                'descripcion' => 'Máquinas de cardio de última tecnología, zona de pesas libres, vestidores y dispensador de agua.',
                'imagen' => 'areas_comunes/gimnasio.jpg',
                'capacidad' => 20,
                'costo_reserva' => 0.00,
                'normas' => 'Uso obligatorio de toalla individual y calzado deportivo adecuado.',
                'activo' => true,
            ]),
            AreaComun::create([
                'nombre' => 'Cancha de Pádel Panorámica',
                'descripcion' => 'Cancha reglamentaria de pádel con césped sintético e iluminación LED nocturna.',
                'imagen' => 'areas_comunes/padel.jpg',
                'capacidad' => 8,
                'costo_reserva' => 15.00,
                'normas' => 'Turnos de máximo 2 horas por departamento para garantizar rotación equitativa.',
                'activo' => true,
            ]),
        ];

        // 5. Create Expensas across 3 months (Current Month, Last Month, 2 Months Ago)
        $now = Carbon::now();
        $meses = [
            $now->copy()->subMonths(2),
            $now->copy()->subMonths(1),
            $now->copy(),
        ];

        foreach ($departamentos as $index => $depto) {
            // Only issue expensas to occupied departments or all
            if (!$depto->propietario_id) {
                continue;
            }

            foreach ($meses as $mIndex => $mDate) {
                $mes = (int) $mDate->format('m');
                $anio = (int) $mDate->format('Y');

                // Determine realistic status
                if ($mIndex === 0) {
                    // 2 months ago: 90% paid, 10% atrasado
                    $estado = ($index % 8 === 0) ? 'atrasado' : 'pagado';
                } elseif ($mIndex === 1) {
                    // Last month: 80% paid, 20% atrasado
                    $estado = ($index % 5 === 0) ? 'atrasado' : 'pagado';
                } else {
                    // Current month: 50% pagado, 50% pendiente
                    $estado = ($index % 2 === 0) ? 'pagado' : 'pendiente';
                }

                $fechaPago = ($estado === 'pagado') ? $mDate->copy()->setDay(rand(5, 20))->toDateString() : null;
                $metodoPago = ($estado === 'pagado') ? ['transferencia', 'qr', 'tarjeta'][rand(0, 2)] : null;
                $comprobante = ($estado === 'pagado') ? 'comprobantes/recibo_muestra.jpg' : null;

                Expensa::create([
                    'departamento_id' => $depto->id,
                    'mes' => $mes,
                    'anio' => $anio,
                    'monto' => 150.00,
                    'estado' => $estado,
                    'fecha_pago' => $fechaPago,
                    'metodo_pago' => $metodoPago,
                    'comprobante_imagen' => $comprobante,
                    'notas' => 'Expensa ordinaria mensual correspondiente a servicios comunes y administración.',
                ]);
            }
        }

        // 6. Create Reservas (Upcoming and Past)
        Reserva::create([
            'area_comun_id' => $areas[1]->id, // Salón Social
            'residente_id' => $users[0]->id, // Carlos Mendoza
            'fecha_reserva' => Carbon::now()->addDays(3)->toDateString(),
            'hora_inicio' => '18:00',
            'hora_fin' => '23:00',
            'estado' => 'confirmada',
            'motivo' => 'Cena de cumpleaños y reunión familiar.',
        ]);

        Reserva::create([
            'area_comun_id' => $areas[2]->id, // Quincho
            'residente_id' => $users[1]->id, // María Rodríguez
            'fecha_reserva' => Carbon::now()->addDays(5)->toDateString(),
            'hora_inicio' => '12:00',
            'hora_fin' => '16:00',
            'estado' => 'confirmada',
            'motivo' => 'Almuerzo parrillada de fin de semana.',
        ]);

        Reserva::create([
            'area_comun_id' => $areas[4]->id, // Pádel
            'residente_id' => $users[2]->id, // Roberto Gómez
            'fecha_reserva' => Carbon::now()->addDays(1)->toDateString(),
            'hora_inicio' => '19:00',
            'hora_fin' => '21:00',
            'estado' => 'confirmada',
            'motivo' => 'Partido amistoso de pádel.',
        ]);

        // 7. Create Gastos de Administración
        $gastos = [
            ['desc' => 'Factura de Energía Eléctrica (Iluminación pasillos, bombas y áreas comunes)', 'monto' => 420.50, 'cat' => 'servicios', 'diasAtras' => 5],
            ['desc' => 'Servicio de Agua Potable y Alcantarillado (Riego y alberca)', 'monto' => 210.00, 'cat' => 'servicios', 'diasAtras' => 8],
            ['desc' => 'Mantenimiento preventivo mensual de 2 ascensores Torre A y B', 'monto' => 450.00, 'cat' => 'mantenimiento', 'diasAtras' => 12],
            ['desc' => 'Empresa de Seguridad y Vigilancia 24/7 (Turnos diurno y nocturno)', 'monto' => 1350.00, 'cat' => 'seguridad', 'diasAtras' => 15],
            ['desc' => 'Insumos químicos y mantenimiento de filtros de piscina', 'monto' => 180.00, 'cat' => 'limpieza', 'diasAtras' => 20],
            ['desc' => 'Reparación de cerradura magnética y brazo hidráulico de portón principal', 'monto' => 135.00, 'cat' => 'reparaciones', 'diasAtras' => 25],
            ['desc' => 'Servicio de jardinería, poda y abono de áreas verdes', 'monto' => 220.00, 'cat' => 'mantenimiento', 'diasAtras' => 32],
            ['desc' => 'Compra de artículos de aseo y desinfección de pasillos y hall', 'monto' => 165.00, 'cat' => 'limpieza', 'diasAtras' => 38],
        ];

        foreach ($gastos as $g) {
            Gasto::create([
                'descripcion' => $g['desc'],
                'monto' => $g['monto'],
                'categoria' => $g['cat'],
                'fecha' => Carbon::now()->subDays($g['diasAtras'])->toDateString(),
                'comprobante' => 'comprobantes/recibo_muestra.jpg',
                'registrado_por' => $admin->id,
            ]);
        }
    }
}
