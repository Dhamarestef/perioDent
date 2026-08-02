<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cedula_profesional')->nullable()->after('bio');
            $table->string('especialidad')->nullable()->after('cedula_profesional');
            $table->text('cedula_especialidad')->nullable()->after('especialidad');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cedula_profesional', 'especialidad', 'cedula_especialidad']);
        });
    }
};
