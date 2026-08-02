<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->foreignId('treatment_id')->nullable()->constrained('treatments')->onDelete('set null');
            $table->string('concepto');
            $table->unsignedInteger('cantidad')->default(1);
            $table->decimal('costo_unitario', 10, 2)->nullable(); // null = "Por cotizar"
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_items');
    }
};
