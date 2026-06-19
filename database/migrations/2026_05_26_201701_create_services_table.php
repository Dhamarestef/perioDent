<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Nombre del servicio
            $table->text('description')->nullable();   // Descripción
            $table->decimal('price', 10, 2);          // Precio
            $table->integer('duration')->default(30);  // Duración en minutos
            $table->integer('buffer_time')->default(0); // Tiempo de descanso
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
