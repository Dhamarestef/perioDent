<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odontogram_treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('tooth_number');
            $table->foreignId('treatment_id')->constrained('treatments')->onDelete('cascade');
            $table->text('observaciones')->nullable();
            $table->boolean('finalizado')->default(false);
            $table->date('fecha_realizacion')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'tooth_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odontogram_treatments');
    }
};
