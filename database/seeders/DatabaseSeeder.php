<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Schedule;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear servicios de ejemplo
        Service::create([
            'name' => 'Consulta General',
            'description' => 'Consulta médica general de 30 minutos',
            'price' => 50.00,
            'duration' => 30,
            'buffer_time' => 5,
        ]);

        Service::create([
            'name' => 'Revisión Especializada',
            'description' => 'Revisión especializada de 60 minutos',
            'price' => 100.00,
            'duration' => 60,
            'buffer_time' => 10,
        ]);

        Service::create([
            'name' => 'Tratamiento',
            'description' => 'Sesión de tratamiento de 45 minutos',
            'price' => 75.00,
            'duration' => 45,
            'buffer_time' => 5,
        ]);

        // Crear horarios (Lunes a Viernes)
        for ($day = 0; $day < 5; $day++) {
            Schedule::create([
                'day_of_week' => $day,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'is_active' => true,
            ]);
        }

        // Sábado
        Schedule::create([
            'day_of_week' => 5,
            'start_time' => '09:00',
            'end_time' => '13:00',
            'is_active' => true,
        ]);

        // Domingo (cerrado)
        Schedule::create([
            'day_of_week' => 6,
            'start_time' => '00:00',
            'end_time' => '00:00',
            'is_active' => false,
        ]);
    }
}
