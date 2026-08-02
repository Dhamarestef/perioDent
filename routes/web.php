<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\OdontogramController;
use App\Http\Controllers\PrescriptionController;

// ── Públicas ──────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);

Route::get('/forgot-password',        [PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/forgot-password',       [PasswordResetController::class, 'sendResetLink'])->name('password.send-reset-link');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password',        [PasswordResetController::class, 'updatePassword'])->name('password.update');

Route::get('/confirm-appointment/{token}', [AppointmentController::class, 'confirm'])->name('appointment.confirm');

// ── Protegidas ────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile/edit',      [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update',   [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{id}',      [ProfileController::class, 'show'])->name('profile.show');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── Pacientes ──
    Route::get('/patients/search',         [PatientController::class, 'search'])->name('patients.search');
    Route::post('/patients/quick-store',   [PatientController::class, 'quickStore'])->name('patients.quick-store');
    Route::get('/patients',                [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create',         [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients',               [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{id}',           [PatientController::class, 'show'])->name('patients.show');
    Route::get('/patients/{id}/edit',      [PatientController::class, 'edit'])->name('patients.edit');
    Route::put('/patients/{id}',           [PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{id}',        [PatientController::class, 'destroy'])->name('patients.destroy');

    // ── Odontograma ──
    Route::get('/patients/{patient}/odontograma', [OdontogramController::class, 'show'])->name('odontogram.show');
    Route::put('/patients/{patient}/odontograma/tooth/{tooth}', [OdontogramController::class, 'updateEstado'])->name('odontogram.tooth.update');
    Route::post('/patients/{patient}/odontograma/tooth/{tooth}/treatments', [OdontogramController::class, 'storeTreatment'])->name('odontogram.treatments.store');
    Route::put('/odontograma/treatments/{treatment}', [OdontogramController::class, 'updateTreatment'])->name('odontogram.treatments.update');
    Route::delete('/odontograma/treatments/{treatment}', [OdontogramController::class, 'destroyTreatment'])->name('odontogram.treatments.destroy');

    // ── Recetas médicas ──
    Route::get('/patients/{patient}/prescriptions/create', [PrescriptionController::class, 'create'])->name('prescriptions.create');
    Route::post('/patients/{patient}/prescriptions',        [PrescriptionController::class, 'store'])->name('prescriptions.store');
    Route::get('/prescriptions/{prescription}',              [PrescriptionController::class, 'show'])->name('prescriptions.show');
    Route::get('/prescriptions/{prescription}/pdf',          [PrescriptionController::class, 'pdf'])->name('prescriptions.pdf');
    Route::get('/prescriptions/{prescription}/duplicate',    [PrescriptionController::class, 'duplicate'])->name('prescriptions.duplicate');
    Route::delete('/prescriptions/{prescription}',           [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');

    // ── Citas (calendario) ──
    Route::get('/appointments/events',              [AppointmentController::class, 'events'])->name('appointment.events');
    Route::get('/appointments/search-quick',         [AppointmentController::class, 'searchAppointments'])->name('appointment.search-quick');
    Route::get('/appointments/api/available-slots',  [AppointmentController::class, 'getAvailableSlots'])->name('appointment.slots');
    Route::post('/appointments/quick',               [AppointmentController::class, 'storeQuick'])->name('appointment.quick-store');
    Route::get('/appointments',                      [AppointmentController::class, 'index'])->name('appointment.index');
    Route::get('/appointments/create',               [AppointmentController::class, 'create'])->name('appointment.create');
    Route::post('/appointments',                     [AppointmentController::class, 'store'])->name('appointment.store');
    Route::put('/appointments/{appointment}/dates',   [AppointmentController::class, 'updateDates'])->name('appointment.update-dates');
    Route::put('/appointments/{appointment}/details', [AppointmentController::class, 'updateDetails'])->name('appointment.update-details');
    Route::put('/appointments/{appointment}/status',  [AppointmentController::class, 'updateStatus'])->name('appointment.update-status');
    Route::delete('/appointments/{appointment}/hard-delete', [AppointmentController::class, 'destroy'])->name('appointment.destroy');
    Route::get('/appointments/{appointment}',        [AppointmentController::class, 'show'])->name('appointment.show');
    Route::get('/appointments/{appointment}/edit',   [AppointmentController::class, 'edit'])->name('appointment.edit');
    Route::put('/appointments/{appointment}',        [AppointmentController::class, 'update'])->name('appointment.update');
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
    Route::get('/medications/search-catalog', [MedicationController::class, 'searchCatalog'])->name('medications.search-catalog');
    Route::get('/medications',           [MedicationController::class, 'index'])->name('medications.index');
    Route::get('/medications/create',    [MedicationController::class, 'create'])->name('medications.create');
    Route::post('/medications',          [MedicationController::class, 'store'])->name('medications.store');
    Route::get('/medications/{id}',      [MedicationController::class, 'show'])->name('medications.show');
    Route::get('/medications/{id}/edit', [MedicationController::class, 'edit'])->name('medications.edit');
    Route::put('/medications/{id}',      [MedicationController::class, 'update'])->name('medications.update');
    Route::delete('/medications/{id}',   [MedicationController::class, 'destroy'])->name('medications.destroy');
});
