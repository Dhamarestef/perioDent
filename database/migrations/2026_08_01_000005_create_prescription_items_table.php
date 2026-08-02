<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->onDelete('cascade');
            $table->foreignId('medication_id')->nullable()->constrained('medications')->onDelete('set null');
            $table->string('nombre_comercial');
            $table->string('principio_activo')->nullable();
            $table->string('presentacion')->nullable();
            $table->string('dosis')->nullable();
            $table->text('indicaciones')->nullable();
            $table->string('frecuencia')->nullable();
            $table->string('duracion')->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
