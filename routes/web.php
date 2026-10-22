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
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\TrashController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\UserManagementController;

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

// Enlace firmado que llega en los correos (confirmación y recordatorio) para cancelar la cita
Route::get('/cancel-appointment/{appointment}', [AppointmentController::class, 'cancelPublic'])
    ->name('appointment.cancel-public')
    ->middleware('signed');

Route::get('/cron/recordatorios/{token}', function (string $token) {
    if (! hash_equals((string) config('app.cron_secret'), $token)) {
        abort(403);
    }

    Artisan::call('appointments:send-email-reminders');

    return response('ok: ' . Artisan::output());
});

// ── Protegidas ────────────────────────────────────────────
Route::middleware(['auth', 'active', 'log.activity'])->group(function () {

    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::get('/profile/edit',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{id}',    [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/logout',         [AuthController::class, 'logout'])->name('logout');

    // ── Gestión de recepcionistas (solo doctor) ──
    Route::middleware('doctor')->prefix('usuarios')->name('users.')->group(function () {
        Route::get('/',                  [UserManagementController::class, 'index'])->name('index');
        Route::get('/actividad',         [UserManagementController::class, 'activity'])->name('activity');
        Route::get('/create',            [UserManagementController::class, 'create'])->name('create');
        Route::post('/',                 [UserManagementController::class, 'store'])->name('store');
        Route::get('/{id}/edit',         [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{id}',              [UserManagementController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle',     [UserManagementController::class, 'toggle'])->name('toggle');
        Route::put('/{id}/password',     [UserManagementController::class, 'resetPassword'])->name('password');
    });

    // ── Pacientes ──
    Route::get('/patients/search',       [PatientController::class, 'search'])->middleware('can:patients.search')->name('patients.search');
    Route::post('/patients/quick-store', [PatientController::class, 'quickStore'])->middleware('can:patients.create')->name('patients.quick-store');
    Route::get('/patients',              [PatientController::class, 'index'])->middleware('can:patients.view')->name('patients.index');
    Route::get('/patients/create',       [PatientController::class, 'create'])->middleware('can:patients.create')->name('patients.create');
    Route::post('/patients',             [PatientController::class, 'store'])->middleware('can:patients.create')->name('patients.store');
    Route::get('/patients/{id}',         [PatientController::class, 'show'])->middleware('can:patients.view')->name('patients.show');
    Route::get('/patients/{id}/edit',    [PatientController::class, 'edit'])->middleware('can:patients.edit')->name('patients.edit');
    Route::put('/patients/{id}',         [PatientController::class, 'update'])->middleware('can:patients.edit')->name('patients.update');
    Route::delete('/patients/{id}',      [PatientController::class, 'destroy'])->middleware('can:patients.delete')->name('patients.destroy');

    // ── Odontograma ──
    Route::get('/patients/{patient}/odontograma', [OdontogramController::class, 'show'])->middleware('can:odontogram.view')->name('odontogram.show');
    Route::middleware('can:odontogram.edit')->group(function () {
        Route::put('/patients/{patient}/odontograma/tooth/{tooth}', [OdontogramController::class, 'updateEstado'])->name('odontogram.tooth.update');
        Route::post('/patients/{patient}/odontograma/tooth/{tooth}/treatments', [OdontogramController::class, 'storeTreatment'])->name('odontogram.treatments.store');
        Route::put('/odontograma/treatments/{treatment}', [OdontogramController::class, 'updateTreatment'])->name('odontogram.treatments.update');
        Route::delete('/odontograma/treatments/{treatment}', [OdontogramController::class, 'destroyTreatment'])->name('odontogram.treatments.destroy');
    });

    // ── Recetas: crear/eliminar solo doctor (llevan su firma y cédulas) ──
    Route::middleware('doctor')->group(function () {
        Route::get('/patients/{patient}/prescriptions/create', [PrescriptionController::class, 'create'])->name('prescriptions.create');
        Route::post('/patients/{patient}/prescriptions',       [PrescriptionController::class, 'store'])->name('prescriptions.store');
        Route::delete('/prescriptions/{prescription}',         [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');
    });
    Route::middleware('can:prescriptions.view')->group(function () {
        Route::get('/prescriptions/{prescription}',     [PrescriptionController::class, 'show'])->name('prescriptions.show');
        Route::get('/prescriptions/{prescription}/pdf', [PrescriptionController::class, 'pdf'])->name('prescriptions.pdf');
    });

    // ── Presupuestos ──
    Route::get('/patients/{patient}/budgets/create', [BudgetController::class, 'create'])->middleware('can:budgets.create')->name('budgets.create');
    Route::post('/patients/{patient}/budgets',       [BudgetController::class, 'store'])->middleware('can:budgets.create')->name('budgets.store');
    Route::get('/budgets/{budget}',                  [BudgetController::class, 'show'])->middleware('can:budgets.view')->name('budgets.show');
    Route::get('/budgets/{budget}/pdf',              [BudgetController::class, 'pdf'])->middleware('can:budgets.view')->name('budgets.pdf');
    Route::get('/budgets/{budget}/edit',             [BudgetController::class, 'edit'])->middleware('can:budgets.edit')->name('budgets.edit');
    Route::put('/budgets/{budget}',                  [BudgetController::class, 'update'])->middleware('can:budgets.edit')->name('budgets.update');
    Route::delete('/budgets/{budget}',               [BudgetController::class, 'destroy'])->middleware('can:budgets.delete')->name('budgets.destroy');

    // ── Citas ──
    Route::get('/appointments/events',       [AppointmentController::class, 'events'])->middleware('can:appointments.view')->name('appointment.events');
    Route::get('/appointments/search-quick', [AppointmentController::class, 'searchAppointments'])->middleware('can:appointments.view')->name('appointment.search-quick');
    Route::post('/appointments/quick',       [AppointmentController::class, 'storeQuick'])->middleware('can:appointments.create')->name('appointment.quick-store');
    Route::get('/appointments',              [AppointmentController::class, 'index'])->middleware('can:appointments.view')->name('appointment.index');
    Route::get('/appointments/create',       [AppointmentController::class, 'create'])->middleware('can:appointments.create')->name('appointment.create');
    Route::post('/appointments',             [AppointmentController::class, 'store'])->middleware('can:appointments.create')->name('appointment.store');
    Route::put('/appointments/{appointment}/dates',   [AppointmentController::class, 'updateDates'])->middleware('can:appointments.edit')->name('appointment.update-dates');
    Route::put('/appointments/{appointment}/details', [AppointmentController::class, 'updateDetails'])->middleware('can:appointments.edit')->name('appointment.update-details');
    // El permiso (editar o cancelar) se valida dentro del controlador según el estado enviado
    Route::put('/appointments/{appointment}/status',  [AppointmentController::class, 'updateStatus'])->name('appointment.update-status');
    Route::delete('/appointments/{appointment}/hard-delete', [AppointmentController::class, 'destroy'])->middleware('can:appointments.delete')->name('appointment.destroy');
    Route::get('/appointments/{appointment}',      [AppointmentController::class, 'show'])->middleware('can:appointments.view')->name('appointment.show');
    Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->middleware('can:appointments.edit')->name('appointment.edit');
    Route::put('/appointments/{appointment}',      [AppointmentController::class, 'update'])->middleware('can:appointments.edit')->name('appointment.update');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->middleware('can:appointments.cancel')->name('appointment.cancel');

    // ── Catálogos ──
    Route::middleware('can:treatments.manage')->group(function () {
        Route::get('/treatments',           [TreatmentController::class, 'index'])->name('treatments.index');
        Route::get('/treatments/create',    [TreatmentController::class, 'create'])->name('treatments.create');
        Route::post('/treatments',          [TreatmentController::class, 'store'])->name('treatments.store');
        Route::get('/treatments/{id}',      [TreatmentController::class, 'show'])->name('treatments.show');
        Route::get('/treatments/{id}/edit', [TreatmentController::class, 'edit'])->name('treatments.edit');
        Route::put('/treatments/{id}',      [TreatmentController::class, 'update'])->name('treatments.update');
        Route::delete('/treatments/{id}',   [TreatmentController::class, 'destroy'])->name('treatments.destroy');
    });

    Route::middleware('can:medications.manage')->group(function () {
        Route::get('/medications/search-catalog', [MedicationController::class, 'searchCatalog'])->name('medications.search-catalog');
        Route::get('/medications',           [MedicationController::class, 'index'])->name('medications.index');
        Route::get('/medications/create',    [MedicationController::class, 'create'])->name('medications.create');
        Route::post('/medications',          [MedicationController::class, 'store'])->name('medications.store');
        Route::get('/medications/{id}',      [MedicationController::class, 'show'])->name('medications.show');
        Route::get('/medications/{id}/edit', [MedicationController::class, 'edit'])->name('medications.edit');
        Route::put('/medications/{id}',      [MedicationController::class, 'update'])->name('medications.update');
        Route::delete('/medications/{id}',   [MedicationController::class, 'destroy'])->name('medications.destroy');
    });

    // ── Papelera (solo doctor: incluye borrado definitivo) ──
    Route::middleware('doctor')->group(function () {
        Route::get('/papelera', [TrashController::class, 'index'])->name('papelera.index');
        Route::delete('/papelera', [TrashController::class, 'empty'])->name('papelera.empty');
        Route::post('/papelera/{tipo}/{id}/restaurar', [TrashController::class, 'restore'])->name('papelera.restore');
        Route::delete('/papelera/{tipo}/{id}', [TrashController::class, 'forceDelete'])->name('papelera.force-delete');
    });
});
