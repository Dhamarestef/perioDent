<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('nombre_temporal')->nullable()->after('patient_id');
            $table->string('telefono_temporal')->nullable()->after('nombre_temporal');
            $table->dateTime('end_date')->nullable()->after('appointment_date');
        });

        // Normaliza los estados anteriores al nuevo vocabulario en español
        DB::table('appointments')->where('status', 'pending')->update(['status' => 'programada']);
        DB::table('appointments')->where('status', 'confirmed')->update(['status' => 'confirmada']);
        DB::table('appointments')->where('status', 'cancelled')->update(['status' => 'cancelada']);
    }

    public function down(): void
    {
        DB::table('appointments')->where('status', 'programada')->update(['status' => 'pending']);
        DB::table('appointments')->where('status', 'confirmada')->update(['status' => 'confirmed']);
        DB::table('appointments')->where('status', 'cancelada')->update(['status' => 'cancelled']);

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['nombre_temporal', 'telefono_temporal', 'end_date']);
        });
    }
};
