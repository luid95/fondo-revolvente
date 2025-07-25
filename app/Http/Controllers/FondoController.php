<?php

namespace App\Http\Controllers;

use App\Models\Fondo;
use App\Models\Solicitud;
use App\Models\Area;
use Illuminate\Http\Request;

class FondoController extends Controller
{
    // Mostrar el monto actual (index)
    public function index(Request $request)
    {
        $fondo = Fondo::latest()->first();
        $monto = $fondo && !$fondo->deleted_at ? $fondo->monto : 0;

        // Filtrado
        $query = Area::whereNull('deleted_at');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%$search%")
                ->orWhere('id', $search); // Búsqueda exacta por ID
            });
        }

        $areas = $query->orderBy('id')->paginate(5);

        return view('fondo.index', compact('monto', 'areas'));
    }

    // Mostrar formulario de edición (edit)
    public function edit()
    {
        $monto = Fondo::latest()->first()?->monto ?? 0;

        return view('fondo.edit', compact( 'monto'));
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
