<?php

return [

    'catalog' => [
        'Citas' => [
            'appointments.view'   => 'Ver la agenda de citas',
            'appointments.create' => 'Agendar citas',
            'appointments.edit'   => 'Editar citas (fecha, hora, notas, estado)',
            'appointments.cancel' => 'Cancelar citas',
            'appointments.delete' => 'Eliminar citas definitivamente',
        ],
        'Pacientes' => [
            'patients.search' => 'Buscar pacientes (necesario para agendar)',
            'patients.view'   => 'Ver lista y expediente administrativo',
            'patients.create' => 'Registrar pacientes',
            'patients.edit'   => 'Editar datos de pacientes',
            'patients.delete' => 'Ocultar pacientes',
        ],
        'Presupuestos' => [
            'budgets.view'   => 'Ver / imprimir presupuestos',
            'budgets.create' => 'Crear presupuestos',
            'budgets.edit'   => 'Editar presupuestos',
            'budgets.delete' => 'Ocultar presupuestos',
        ],
        'Información clínica' => [
            'odontogram.view'   => 'Ver odontograma',
            'odontogram.edit'   => 'Modificar odontograma',
            'prescriptions.view' => 'Ver / imprimir recetas',
        ],
        'Catálogos' => [
            'treatments.manage'  => 'Administrar catálogo de tratamientos',
            'medications.manage' => 'Administrar catálogo de medicamentos',
        ],
    ],

    // Permisos con los que nace una recepcionista (el doctor puede cambiarlos).
    'receptionist_defaults' => [
        'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.cancel',
        'patients.search', 'patients.view', 'patients.create',
        'budgets.view', 'budgets.create',
    ],

    // Peticiones GET que también se registran en la bitácora (las demás
    // peticiones de escritura se registran siempre).
    'logged_get_routes' => [
        'budgets.pdf', 'prescriptions.show', 'prescriptions.pdf', 'odontogram.show',
    ],

    'activity_labels' => [
        'login'                      => 'Inició sesión',
        'logout'                     => 'Cerró sesión',
        'appointment.quick-store'    => 'Agendó una cita',
        'appointment.store'          => 'Agendó una cita',
        'appointment.update-dates'   => 'Movió / redimensionó una cita',
        'appointment.update-details' => 'Editó los detalles de una cita',
        'appointment.update-status'  => 'Cambió el estado de una cita',
        'appointment.update'         => 'Editó una cita',
        'appointment.cancel'         => 'Canceló una cita',
        'appointment.destroy'        => 'Eliminó una cita',
        'patients.store'             => 'Registró un paciente',
        'patients.quick-store'       => 'Registró un paciente (rápido)',
        'patients.update'            => 'Editó un paciente',
        'patients.destroy'           => 'Ocultó un paciente',
        'budgets.store'              => 'Creó un presupuesto',
        'budgets.update'             => 'Editó un presupuesto',
        'budgets.destroy'            => 'Ocultó un presupuesto',
        'budgets.pdf'                => 'Descargó el PDF de un presupuesto',
        'prescriptions.show'         => 'Consultó una receta',
        'prescriptions.pdf'          => 'Descargó el PDF de una receta',
        'odontogram.show'            => 'Consultó un odontograma',
        'odontogram.tooth.update'    => 'Cambió el estado de un diente',
        'odontogram.treatments.store'   => 'Agregó un tratamiento al odontograma',
        'odontogram.treatments.update'  => 'Editó un tratamiento del odontograma',
        'odontogram.treatments.destroy' => 'Quitó un tratamiento del odontograma',
        'profile.update'             => 'Actualizó su perfil',
    ],
];
