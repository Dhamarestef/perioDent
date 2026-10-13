#!/usr/bin/env python3
"""
Registra las rutas de la papelera y agrega la tarjeta al dashboard.
Ejecutar desde la raíz del proyecto Laravel:  python3 aplicar_papelera.py
"""
import os, sys

CAMBIOS = {
    "routes/web.php": [
        ("use App\\Http\\Controllers\\BudgetController;\n",
         "use App\\Http\\Controllers\\BudgetController;\nuse App\\Http\\Controllers\\TrashController;\n",
         "TrashController"),
        ("    Route::delete('/medications/{id}',   [MedicationController::class, 'destroy'])->name('medications.destroy');\n",
         "    Route::delete('/medications/{id}',   [MedicationController::class, 'destroy'])->name('medications.destroy');\n"
         "\n    // ── Papelera ──\n"
         "    Route::get('/papelera', [TrashController::class, 'index'])->name('papelera.index');\n"
         "    Route::post('/papelera/{tipo}/{id}/restaurar', [TrashController::class, 'restore'])->name('papelera.restore');\n",
         "papelera.index"),
    ],
    "resources/views/dashboard.blade.php": [
        ("            <a href=\"{{ route('profile.edit') }}\" class=\"card-link\">",
         "            <a href=\"{{ route('papelera.index') }}\" class=\"card-link\">\n"
         "                <div class=\"card\">\n"
         "                    <div class=\"card-icon\">🗑</div>\n"
         "                    <h3>Papelera</h3>\n"
         "                    <p>Pacientes, presupuestos y recetas ocultados</p>\n"
         "                </div>\n"
         "            </a>\n\n"
         "            <a href=\"{{ route('profile.edit') }}\" class=\"card-link\">",
         "papelera.index"),
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
    crlf = "\r\n" in texto
    texto = texto.replace("\r\n", "\n")
    for viejo, nuevo, marca in reemplazos:
        if marca in texto and viejo not in texto:
            print(f"[YA]     {ruta}: ya estaba aplicado")
        elif viejo in texto:
            texto = texto.replace(viejo, nuevo, 1)
            print(f"[OK]     {ruta}")
        else:
            print(f"[NO ENCONTRADO] {ruta}: {viejo.strip()[:60]}...")
            errores += 1
    if crlf:
        texto = texto.replace("\n", "\r\n")
    with open(ruta, "w", encoding="utf-8", newline="") as f:
        f.write(texto)

print("\nListo." if not errores else f"\nTerminó con {errores} aviso(s), revísalos.")
sys.exit(1 if errores else 0)
