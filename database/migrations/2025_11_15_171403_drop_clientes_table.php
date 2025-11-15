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
        // Eliminar la tabla clientes ya que usaremos la tabla users
        Schema::dropIfExists('clientes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recrear la tabla clientes en caso de rollback
        Schema::create('clientes', function (Blueprint $table) {
            $table->integer('codigo_cliente')->primary();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('telefono')->nullable();
            $table->string('domicilio')->nullable();
            $table->timestamps();
        });
    }
};
