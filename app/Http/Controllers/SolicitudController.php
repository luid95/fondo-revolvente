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
            'fecha' => 'required|date',
            'area' => 'required|numeric',
            'personas' => 'required|string',
            'uso' => 'required|string',
            'monto' => 'required|numeric',
        ]);

        $tags = json_decode($request->personas, true); // decode a array de objetos
        $nombres = array_column($tags, 'value'); // extrae sólo los valores
        $personas_string = implode('\\', $nombres); // convierte a string: "Luis,María,Pedro"

        $solicitud = new Solicitud();
        $solicitud->fecha = $request->fecha;
        $solicitud->area_id = $request->area;
        $solicitud->personas = $personas_string;
        $solicitud->uso = $request->uso;
        $solicitud->monto = $request->monto;
        $solicitud->saldo_restante = 0;
        $solicitud->tipo = 'normal';
        $solicitud->estado = 'en proceso';
        $solicitud->save();

        $this->recalcularSaldos();
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

        $this->recalcularSaldos();

        return redirect()->route('solicitud.index');
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

    protected function recalcularSaldos()
    {
        $fondo = Fondo::latest()->whereNull('deleted_at')->first();
        $montoDisponible = $fondo ? $fondo->monto : 0;

        // Obtener solicitudes ordenadas por fecha ascendente (o por id si quieres)
        $solicitudes = Solicitud::orderBy('fecha')->orderBy('id')->get();

        $saldo = $montoDisponible;
        

        foreach ($solicitudes as $solicitud) {

            if ($solicitud->tipo === 'reposicion') {
                $saldo += $solicitud->monto; // 💸 reposición repone fondos
                
            } 
            
            if ($solicitud->tipo === 'normal') {
                $saldo -= $solicitud->monto; // 🧾 solicitud normal descuenta fondos
            }

            $solicitud->saldo_restante = max($saldo, 0); // evitar saldo negativo
            
            $solicitud->save();
        }
    }

}
