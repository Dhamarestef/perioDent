<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Listar servicios
     */
    public function index()
    {
        $services = Service::all();
        return view('services.index', compact('services'));
    }

    /**
     * Crear servicio
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:15',
            'buffer_time' => 'nullable|integer|min:0',
        ]);

        Service::create($request->all());

        return back()->with('success', 'Servicio creado exitosamente');
    }

    /**
     * Actualizar servicio
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:15',
            'buffer_time' => 'nullable|integer|min:0',
        ]);

        $service->update($request->all());

        return back()->with('success', 'Servicio actualizado correctamente');
    }

    /**
     * Eliminar servicio
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Servicio eliminado correctamente');
    }
}
