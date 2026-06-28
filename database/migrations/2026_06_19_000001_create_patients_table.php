<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('numero_expediente')->unique();
            $table->date('fecha_registro');
            $table->string('nombre');
            $table->integer('edad');
            $table->enum('sexo', ['F', 'M']);
            $table->enum('escolaridad', [
                'Primaria',
                'Secundaria',
                'Preparatoria',
                'Licenciatura',
                'Especialidad',
                'Otro',
            ]);
            $table->enum('estado_civil', [
                'Soltero(a)',
                'Casado(a)',
                'Divorciado(a)',
                'Viudo(a)',
                'Unión libre',
                'Otro',
            ]);
            $table->string('lugar_nacimiento');
            $table->date('fecha_nacimiento');
            $table->string('domicilio_actual');
            $table->string('telefono')->nullable();
            $table->string('lugar_residencia');
            $table->string('correo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
