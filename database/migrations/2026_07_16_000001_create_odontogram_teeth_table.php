<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odontogram_teeth', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('tooth_number');
            $table->enum('estado', ['normal', 'ausente', 'pendiente', 'finalizado'])->default('normal');
            $table->timestamps();

            $table->unique(['patient_id', 'tooth_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odontogram_teeth');
    }
};
