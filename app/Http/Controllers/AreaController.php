<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaController extends Controller
{
    // NUEVO: Mostrar formulario para crear
    public function create()
    {
        return view('area.create');
    }

    // NUEVO: Guardar nueva área
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:50',
            'siglas' => 'nullable|string|max:20',
        ]);

        // Buscar área con nombre o siglas iguales, sin importar si está eliminada o no
        $areaExistente = $this->buscarAreaExistente($data);

        if ($areaExistente) {
            if ($areaExistente->trashed()) {
                // Ya existía pero fue eliminada, mostrar mensaje de recuperación
                return redirect()->route('fondo.index')->with([
                    'warning' => 'El área ya existía pero fue eliminada.',
                    'restore_area_id' => $areaExistente->id,
                ]);
            } else {
                return redirect()->route('fondo.index')->with('error', 'Ya existe un área con ese nombre o siglas.');
            }
        }

        Area::create($data);

        return redirect()->route('fondo.index')->with('success', 'Área creada correctamente.');
    }

    public function restore($id)
    {
        $area = Area::withTrashed()->findOrFail($id);

        if ($area->trashed()) {
            $area->restore();
            return redirect()->route('fondo.index')->with('success', 'Área restaurada correctamente.');
        }

        return redirect()->route('fondo.index')->with('info', 'El área ya estaba activa.');
    }

    // Ya debes tener estas dos funciones:
    public function edit($id)
    {
        $area = Area::findOrFail($id);
        return view('area.edit', compact('area'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:50',
            'siglas' => 'nullable|string|max:20',
        ]);

        $area = Area::findOrFail($id);
        $area->update($request->only('nombre', 'codigo', 'siglas'));

        return redirect()->route('fondo.index')->with('success', 'Área actualizada correctamente.');
    }

    public function destroy( Area $area)
    {
        // Verificar si el área está relacionada con alguna solicitud
        if ($area->solicitudes()->exists()) {
            return redirect()->route('fondo.index')
                ->with('error', 'No se puede eliminar el área porque está asociada a una o más solicitudes.');
        }
        
        $area->delete();

        return redirect()->route('fondo.index')->with('success', 'Área eliminada correctamente.');
    }

    private function buscarAreaExistente(array $data)
    {
        return Area::withTrashed()
            ->where(function ($query) use ($data) {
                $query->where('nombre', $data['nombre'])
                    ->orWhere('siglas', $data['siglas']);
            })->first();
    }
}
