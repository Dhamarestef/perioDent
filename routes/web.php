<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

// Rutas públicas
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rutas de autenticación
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Recuperación de contraseña
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.send-reset-link');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'updatePassword'])->name('password.update');

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // NUEVAS RUTAS DE PERFIL
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// RUTAS DE CITAS
Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointment.index');
Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointment.create');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointment.store');
Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointment.show');
Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointment.edit');
Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointment.update');
Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointment.cancel');
Route::get('/appointments/api/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('appointment.slots');

// RUTAS DE CONFIRMACIÓN (público)
Route::get('/confirm-appointment/{token}', [AppointmentController::class, 'confirm'])->name('appointment.confirm');
