<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_comun_id')->constrained('areas_comunes')->cascadeOnDelete();
            $table->foreignId('residente_id')->constrained('users')->cascadeOnDelete();
            $table->date('fecha_reserva');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->enum('estado', ['confirmada', 'cancelada', 'pendiente'])->default('confirmada');
            $table->text('motivo')->nullable();
            $table->timestamps();

            // Index for fast overlap checks
            $table->index(['area_comun_id', 'fecha_reserva', 'estado'], 'idx_reservas_overlap_check');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
