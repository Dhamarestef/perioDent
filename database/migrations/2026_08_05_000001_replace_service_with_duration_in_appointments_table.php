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
            $table->integer('duration_minutes')->default(30)->after('service_id');
        });

        // Migra la duración desde el servicio anterior (si existía) para no perder información.
        if (Schema::hasColumn('appointments', 'service_id') && Schema::hasTable('services')) {
            DB::table('appointments')
                ->join('services', 'appointments.service_id', '=', 'services.id')
                ->update(['appointments.duration_minutes' => DB::raw('services.duration')]);
        }

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('patient_id')
                  ->constrained('services')->onDelete('cascade');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('duration_minutes');
        });
    }
};
