<?php

namespace App\Http\Controllers;

use App\Models\Fondo;
use App\Models\Solicitud;
use App\Models\Area;
use Illuminate\Http\Request;

class FondoController extends Controller
{
    // Mostrar el monto actual (index)
    public function index()
    {
        $fondo = Fondo::latest()->first();
        $monto = $fondo && !$fondo->deleted_at ? $fondo->monto : 0;

        $areas = Area::whereNull('deleted_at')->orderBy('id')->paginate(5);

        return view('fondo.index', compact('monto', 'areas'));
    }

    // Mostrar formulario de edición (edit)
    public function edit()
    {
        $fondo = Fondo::latest()->first();
        $monto = $fondo ? $fondo->monto : 0;

        return view('fondo.edit', compact('fondo', 'monto'));
    }

    // Guardar cambios (update)
    public function update(Request $request)
    {
        $data = $request->validate([
            'monto' => 'required|numeric|min:0',
        ]);

        $fondo = Fondo::latest()->first();

        if ($fondo) {
            $fondo->update(['monto' => $data['monto']]);
        } else {
            Fondo::create(['monto' => $data['monto']]);
        }

        return redirect()->route('fondo.index')->with('success', 'Fondo actualizado correctamente.');
    }
}
