<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\MedicationController;

// ── Públicas ──────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Autenticación
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);

// Recuperación de contraseña
Route::get('/forgot-password',        [PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/forgot-password',       [PasswordResetController::class, 'sendResetLink'])->name('password.send-reset-link');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password',        [PasswordResetController::class, 'updatePassword'])->name('password.update');

// Confirmación de cita (pública — el paciente accede desde su email)
Route::get('/confirm-appointment/{token}', [AppointmentController::class, 'confirm'])->name('appointment.confirm');

// ── Protegidas ────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Perfil
    Route::get('/profile/edit',      [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update',   [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{id}',      [ProfileController::class, 'show'])->name('profile.show');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── Pacientes ──
    // OJO: search y quick-store ANTES de {id} para que Laravel no los confunda
    Route::get('/patients/search',         [PatientController::class, 'search'])->name('patients.search');
    Route::post('/patients/quick-store',   [PatientController::class, 'quickStore'])->name('patients.quick-store');
    Route::get('/patients',                [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create',         [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients',               [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{id}',           [PatientController::class, 'show'])->name('patients.show');
    Route::get('/patients/{id}/edit',      [PatientController::class, 'edit'])->name('patients.edit');
    Route::put('/patients/{id}',           [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{id}',        [PatientController::class, 'destroy'])->name('patients.destroy');

    // ── Citas ──
    Route::get('/appointments/api/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('appointment.slots');
    Route::get('/appointments',                     [AppointmentController::class, 'index'])->name('appointment.index');
    Route::get('/appointments/create',              [AppointmentController::class, 'create'])->name('appointment.create');
    Route::post('/appointments',                    [AppointmentController::class, 'store'])->name('appointment.store');
    Route::get('/appointments/{appointment}',       [AppointmentController::class, 'show'])->name('appointment.show');
    Route::get('/appointments/{appointment}/edit',  [AppointmentController::class, 'edit'])->name('appointment.edit');
    Route::put('/appointments/{appointment}',       [AppointmentController::class, 'update'])->name('appointment.update');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointment.cancel');

    // ── Tratamientos ──
    Route::get('/treatments',            [TreatmentController::class, 'index'])->name('treatments.index');
    Route::get('/treatments/create',     [TreatmentController::class, 'create'])->name('treatments.create');
    Route::post('/treatments',           [TreatmentController::class, 'store'])->name('treatments.store');
    Route::get('/treatments/{id}',       [TreatmentController::class, 'show'])->name('treatments.show');
    Route::get('/treatments/{id}/edit',  [TreatmentController::class, 'edit'])->name('treatments.edit');
    Route::put('/treatments/{id}',       [TreatmentController::class, 'update'])->name('treatments.update');
    Route::delete('/treatments/{id}',    [TreatmentController::class, 'destroy'])->name('treatments.destroy');

    // ── Medicamentos ──
    Route::get('/medications',           [MedicationController::class, 'index'])->name('medications.index');
    Route::get('/medications/create',    [MedicationController::class, 'create'])->name('medications.create');
    Route::post('/medications',          [MedicationController::class, 'store'])->name('medications.store');
    Route::get('/medications/{id}',      [MedicationController::class, 'show'])->name('medications.show');
    Route::get('/medications/{id}/edit', [MedicationController::class, 'edit'])->name('medications.edit');
    Route::put('/medications/{id}',      [MedicationController::class, 'update'])->name('medications.update');
    Route::delete('/medications/{id}',   [MedicationController::class, 'destroy'])->name('medications.destroy');
});
