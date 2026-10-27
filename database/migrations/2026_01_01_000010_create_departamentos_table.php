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
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_departamento', 20);
            $table->integer('piso')->default(1);
            $table->string('bloque', 50)->nullable();
            $table->foreignId('propietario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Prevents duplicate unit numbers inside the same block
            $table->unique(['numero_departamento', 'bloque'], 'unique_unidad_bloque');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};
