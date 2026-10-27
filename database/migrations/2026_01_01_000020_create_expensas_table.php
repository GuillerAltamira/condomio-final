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
        Schema::create('expensas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained('departamentos')->cascadeOnDelete();
            $table->unsignedSmallInteger('mes'); // 1 - 12
            $table->unsignedSmallInteger('anio'); // e.g. 2026
            $table->decimal('monto', 10, 2);
            $table->enum('estado', ['pendiente', 'pagado', 'atrasado'])->default('pendiente');
            $table->date('fecha_pago')->nullable();
            $table->string('metodo_pago', 50)->nullable();
            $table->string('comprobante_imagen')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            // Constraint: One expensa per department per month & year
            $table->unique(['departamento_id', 'mes', 'anio'], 'unique_expensa_depto_periodo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expensas');
    }
};
