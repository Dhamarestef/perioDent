#!/usr/bin/env python3
"""
Aplica los cambios "ocultar en vez de eliminar" a Appointment.php, los
controladores y las vistas. Ejecutar desde la raíz del proyecto Laravel:

    python3 aplicar_cambios.py
"""
import os, sys

CAMBIOS = {
    "app/Models/Appointment.php": [
        ("return $this->belongsTo(Patient::class);",
         "return $this->belongsTo(Patient::class)->withTrashed();"),
    ],
    "app/Http/Controllers/PatientController.php": [
        ("'Paciente eliminado correctamente'", "'Paciente ocultado correctamente'"),
    ],
    "app/Http/Controllers/BudgetController.php": [
        ("'Presupuesto eliminado correctamente'", "'Presupuesto ocultado correctamente'"),
    ],
    "app/Http/Controllers/PrescriptionController.php": [
        ("'Receta eliminada correctamente'", "'Receta ocultada correctamente'"),
    ],
    "resources/views/patients/index.blade.php": [
        ("confirm('¿Eliminar este paciente? Esta acción no se puede deshacer.')",
         "confirm('¿Ocultar este paciente?')"),
        ('class="btn-sm btn-del">Eliminar</button>',
         'class="btn-sm btn-del">Ocultar</button>'),
    ],
    "resources/views/patients/show.blade.php": [
        ("confirm('¿Eliminar este paciente? Esta acción eliminará también sus citas.')",
         "confirm('¿Ocultar este paciente?')"),
        ("🗑 Eliminar", "🙈 Ocultar"),
    ],
    "resources/views/budgets/show.blade.php": [
        ("confirm('¿Eliminar este presupuesto?')", "confirm('¿Ocultar este presupuesto?')"),
        ("🗑 Eliminar", "🙈 Ocultar"),
    ],
    "resources/views/prescriptions/show.blade.php": [
        ("confirm('¿Eliminar esta receta?')", "confirm('¿Ocultar esta receta?')"),
        ("🗑 Eliminar", "🙈 Ocultar"),
    ],
}

errores = 0
for ruta, reemplazos in CAMBIOS.items():
    if not os.path.exists(ruta):
        print(f"[FALTA]  {ruta} (¿estás en la raíz del proyecto?)")
        errores += 1
        continue
    with open(ruta, encoding="utf-8") as f:
        texto = f.read()
    for viejo, nuevo in reemplazos:
        if nuevo in texto and viejo not in texto:
            print(f"[YA]     {ruta}: ya estaba aplicado")
        elif viejo in texto:
            texto = texto.replace(viejo, nuevo)
            print(f"[OK]     {ruta}")
        else:
            print(f"[NO ENCONTRADO] {ruta}: {viejo[:50]}...")
            errores += 1
    with open(ruta, "w", encoding="utf-8", newline="") as f:
        f.write(texto)

print("\nListo." if not errores else f"\nTerminó con {errores} aviso(s), revísalos.")
print("Ahora corre: php artisan migrate")
sys.exit(1 if errores else 0)
