#!/usr/bin/env python3
"""Agrega la ruta de borrado definitivo. Ejecutar desde la raíz del proyecto."""
import os, sys
ruta = "routes/web.php"
if not os.path.exists(ruta):
    print(f"[FALTA] {ruta} (¿estás en la raíz del proyecto?)"); sys.exit(1)
with open(ruta, encoding="utf-8", newline="") as f:
    t = f.read()
crlf = "\r\n" in t
t = t.replace("\r\n", "\n")
if "papelera.force-delete" in t:
    print("[YA] La ruta ya estaba agregada.")
else:
    viejo = "[TrashController::class, 'restore'])->name('papelera.restore');\n"
    nuevo = viejo + "    Route::delete('/papelera/{tipo}/{id}', [TrashController::class, 'forceDelete'])->name('papelera.force-delete');\n"
    if viejo not in t:
        print("[NO ENCONTRADO] No está la ruta papelera.restore; aplica primero papelera.zip."); sys.exit(1)
    t = t.replace(viejo, nuevo, 1)
    print("[OK] routes/web.php")
if crlf: t = t.replace("\n", "\r\n")
with open(ruta, "w", encoding="utf-8", newline="") as f:
    f.write(t)
print("Listo. Corre: php artisan route:clear")
