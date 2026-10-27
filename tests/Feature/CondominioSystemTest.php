<?php

namespace Tests\Feature;

use App\Models\AreaComun;
use App\Models\Departamento;
use App\Models\Expensa;
use App\Models\Gasto;
use App\Models\Reserva;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class CondominioSystemTest extends TestCase
{
    /**
     * Test that guest is redirected to login.
     */
    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test admin can access all admin sections.
     */
    public function test_admin_can_access_dashboard_and_modules(): void
    {
        $admin = User::where('rol', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/departamentos');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/residentes');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/expensas');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/areas-comunes');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/reservas');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/gastos');
        $response->assertStatus(200);
    }

    /**
     * Test resident cannot access admin-protected routes.
     */
    public function test_resident_cannot_access_admin_routes(): void
    {
        $residente = User::where('rol', 'residente')->first();
        $this->assertNotNull($residente);

        // Allowed resident routes
        $this->actingAs($residente)->get('/dashboard')->assertStatus(200);
        $this->actingAs($residente)->get('/expensas')->assertStatus(200);
        $this->actingAs($residente)->get('/areas-comunes')->assertStatus(200);
        $this->actingAs($residente)->get('/reservas')->assertStatus(200);

        // Forbidden admin routes
        $this->actingAs($residente)->get('/departamentos')->assertRedirect('/dashboard');
        $this->actingAs($residente)->get('/residentes')->assertRedirect('/dashboard');
        $this->actingAs($residente)->get('/gastos')->assertRedirect('/dashboard');
    }

    /**
     * Test reservation time overlap detection logic.
     */
    public function test_reservation_overlap_prevention(): void
    {
        $area = AreaComun::first();
        $fecha = Carbon::now()->addDays(10)->toDateString();
        $residente = User::where('rol', 'residente')->first();

        // Create base confirmed reservation from 14:00 to 18:00
        Reserva::create([
            'area_comun_id' => $area->id,
            'residente_id' => $residente->id,
            'fecha_reserva' => $fecha,
            'hora_inicio' => '14:00',
            'hora_fin' => '18:00',
            'estado' => 'confirmada',
        ]);

        // Overlapping scenarios
        $this->assertTrue(Reserva::existeSolapamiento($area->id, $fecha, '15:00', '17:00'));
        $this->assertTrue(Reserva::existeSolapamiento($area->id, $fecha, '13:00', '15:00'));
        $this->assertTrue(Reserva::existeSolapamiento($area->id, $fecha, '17:00', '19:00'));
        $this->assertTrue(Reserva::existeSolapamiento($area->id, $fecha, '12:00', '20:00'));

        // Non-overlapping scenarios
        $this->assertFalse(Reserva::existeSolapamiento($area->id, $fecha, '09:00', '13:00'));
        $this->assertFalse(Reserva::existeSolapamiento($area->id, $fecha, '19:00', '22:00'));
    }

    /**
     * Test duplicate CI prevention for residents.
     */
    public function test_duplicate_ci_validation(): void
    {
        $admin = User::where('rol', 'admin')->first();
        $existingResident = User::where('rol', 'residente')->first();

        $response = $this->actingAs($admin)->post('/residentes', [
            'name' => 'Otro Residente',
            'email' => 'otro@condominio.com',
            'ci' => $existingResident->ci, // Duplicate CI
            'telefono' => '71111111',
        ]);

        $response->assertSessionHasErrors('ci');
    }
}
