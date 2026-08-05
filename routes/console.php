<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Todos los días a las 10:00 AM, busca las citas de mañana y envía el recordatorio por email
// (con botón para confirmar la cita).
Schedule::command('appointments:send-email-reminders')->dailyAt('10:00');

// Cuando actives WhatsApp/Twilio, descomenta esta línea:
// Schedule::command('appointments:send-whatsapp-reminders')->dailyAt('09:00');
