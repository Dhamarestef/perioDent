<?php
// database/migrations/2026_07_31_000001_add_email_tracking_to_appointments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('email_status')->default('pendiente')->after('email_reminder_sent_at');
            // pendiente | enviado | error
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('email_status');
        });
    }
};
