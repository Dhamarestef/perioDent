<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->string('principio_activo')->nullable()->after('nombre');
            $table->string('presentacion')->nullable()->after('principio_activo');
            $table->string('dosis_habitual')->nullable()->after('presentacion');
        });
    }

    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->dropColumn(['principio_activo', 'presentacion', 'dosis_habitual']);
        });
    }
};
