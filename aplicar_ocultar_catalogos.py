#!/usr/bin/env python3
"""
Cambia los textos "Eliminar" por "Ocultar" en las vistas de tratamientos y
medicamentos, y actualiza la descripción de la papelera en el dashboard.
Ejecutar desde la raíz del proyecto Laravel:

    python3 aplicar_ocultar_catalogos.py
"""
import os, sys

CAMBIOS = {
    "resources/views/treatments/index.blade.php": [
        ("confirm('¿Eliminar este tratamiento?')", "confirm('¿Ocultar este tratamiento?')"),
        ('class="btn-sm btn-del">Eliminar</button>', 'class="btn-sm btn-del">Ocultar</button>'),
    ],
    "resources/views/treatments/show.blade.php": [
        ("confirm('¿Eliminar este tratamiento?')", "confirm('¿Ocultar este tratamiento?')"),
        ("🗑 Eliminar", "🙈 Ocultar"),
    ],
    "resources/views/medications/index.blade.php": [
        ("confirm('¿Eliminar este medicamento?')", "confirm('¿Ocultar este medicamento?')"),
        ('class="btn-sm btn-del">Eliminar</button>', 'class="btn-sm btn-del">Ocultar</button>'),
    ],
    "resources/views/medications/show.blade.php": [
        ("confirm('¿Eliminar este medicamento?')", "confirm('¿Ocultar este medicamento?')"),
        ("🗑 Eliminar", "🙈 Ocultar"),
    ],
    "resources/views/dashboard.blade.php": [
        ("Pacientes, presupuestos y recetas ocultados",
         "Pacientes, presupuestos, recetas, tratamientos y medicamentos ocultados"),
    ],
}

errores = 0
for ruta, reemplazos in CAMBIOS.items():
    if not os.path.exists(ruta):
        print(f"[FALTA]  {ruta} (¿estás en la raíz del proyecto?)")
        errores += 1
        continue
    with open(ruta, encoding="utf-8", newline="") as f:
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
