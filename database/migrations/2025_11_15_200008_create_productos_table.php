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
        Schema::create('productos', function (Blueprint $table) {
            $table->integer('codigo_producto')->primary();
            $table->string('nombre');
            $table->string('imagen')->nullable();
            $table->decimal('precio_unitario', 10, 2);
            $table->string('categoria')->nullable();
            $table->string('unidad_medida')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
