<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\Fondo;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        $query = Solicitud::with('area')
            ->whereNull('deleted_at');

        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $solicitudes = $query->orderBy('id')->paginate(10)->appends($request->query());

        $fondo = Fondo::latest()->first();
        $monto = $fondo && !$fondo->deleted_at ? $fondo->monto : 0;

        // Solo áreas que no estén eliminadas lógicamente
        $areas = Area::whereNull('deleted_at')->get();

        return view('solicitud.index', compact('solicitudes', 'monto', 'areas'));
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'id' => 'required|numeric',
            'fecha' => 'required|date',
            'area' => 'required|numeric',
            'personas' => 'required|string',
            'uso' => 'required|string',
            'monto' => 'required|numeric',
        ]);

        $tags = json_decode($request->personas, true); // decode a array de objetos
        $nombres = array_column($tags, 'value'); // extrae sólo los valores
        $personas_string = implode('\\', $nombres); // convierte a string: "Luis,María,Pedro"
        
        // Buscar área con nombre o siglas iguales, sin importar si está eliminada o no
        $solicitudExistente = Solicitud::withTrashed()
            ->where(function ($query) use ($data) {
                $query->where('id', $data['id']);
            })
            ->first();

        if ($solicitudExistente) {
            if ($solicitudExistente->trashed()) {
                // Ya existía pero fue eliminada, mostrar mensaje de recuperación
                return redirect()->route('solicitud.index')->with([
                    'warning' => 'El ID de ese registro ya existía pero fue eliminada.Por favor eligir otro ID',
                ]);
            } else {
                return redirect()->route('solicitud.index')->with('error', 'Ya existe una solicitud con ese ID.');
            }
        }

        $solicitud = new Solicitud();
        $solicitud->id = $request->id;
        $solicitud->fecha = $request->fecha;
        $solicitud->area_id = $request->area;
        $solicitud->personas = $personas_string;
        $solicitud->uso = $request->uso;
        $solicitud->monto = $request->monto;
        $solicitud->saldo_restante = 0;
        $solicitud->estado = 'en proceso';
        $solicitud->save();

        return redirect()->route('solicitud.index')->with('success', 'Solicitud creada correctamente.');
    }

    public function edit(Solicitud $solicitud)
    {
        $areas = Area::where('deleted_at')->get();

        return view('solicitud.edit', compact('solicitud', 'areas'));
    }

    public function update(Request $request, Solicitud $solicitud)
    {

        $tags = json_decode($request->personas, true); // decode a array de objetos
        $nombres = array_column($tags, 'value'); // extrae sólo los valores
        $personas_string = implode('\\', $nombres); // convierte a string: "Luis,María,Pedro"

        $request->validate([
            'fecha' => 'required|date',
            'area' => 'required|string',
            'personas' => 'required|string',
            'uso' => 'required|string',
            'monto' => 'required|numeric',
        ]);

        $solicitud->fecha = $request->fecha;
        $solicitud->area_id = $request->area;
        $solicitud->personas = $personas_string;;
        $solicitud->uso = $request->uso;
        $solicitud->monto = $request->monto;
        $solicitud->save();

        return redirect()->route('solicitud.index');
        //return redirect()->back();
    }

    public function destroy(Solicitud $solicitud)
    {
        if ($solicitud->facturas()->exists()) {
            return redirect()->route('solicitud.index')
                ->with('error', 'No se puede eliminar la solicitud porque tiene facturas asociadas.');
        }
    
        $solicitud->delete();
        return redirect()->route('solicitud.index')
            ->with('success', 'Solicitud eliminada correctamente.');

    }

    public function facturasPreview($id)
    {
        $solicitud = Solicitud::with('facturas.proveedor', 'area')->findOrFail($id);
        return view('partials.facturas_preview', compact('solicitud'));
    }

}
