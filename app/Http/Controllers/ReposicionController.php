<?php

namespace App\Http\Controllers;

use App\Models\Reposicion;
use App\Models\Solicitud;
use App\Models\Factura;
use Illuminate\Http\Request;

use App\Exports\MultipleRepositionsExport;
use App\Exports\ReposicionExport;
use Maatwebsite\Excel\Facades\Excel;

class ReposicionController extends Controller
{
    public function index()
    {
        $reposiciones = Reposicion::with(['solicitudes.facturas', 'solicitudes.area'])->orderByDesc('id')->get();
        return view('reposicion.index', compact('reposiciones'));
    }

    public function create()
    {
        // Obtener solicitudes disponibles:
        $solicitudes = Solicitud::whereNull('reposicion_id')
            ->whereDoesntHave('facturas', function ($q) {
                $q->where('situacion', '!=', 'Comprobado');
            })
            ->get()
            ->filter(function ($solicitud) {
                $sumaFacturas = $solicitud->facturas->sum('importe');
                return abs($sumaFacturas - $solicitud->monto) <= 1;
            });

        return view('reposicion.create', compact('solicitudes'));
    }

    public function store(Request $request)
    {
        //dd($request->validate);
        $request->validate([
            'nombre_rep' => 'required',
            'n_revolvencia' => 'required|unique:reposiciones,n_revolvencia',
            'fecha_reg' => 'required|date', 
            'solicitudes' => 'required|array'
        ]);
        
        $reposicion = Reposicion::create($request->only(['nombre_rep', 'n_revolvencia', 'fecha_reg']));

        Solicitud::whereIn('id', $request->solicitudes)->update([
            'reposicion_id' => $reposicion->id,
            'estado' => 'Finalizado'
        ]);

        // Cambiar situación de facturas asociadas a esta solicitud a "Comprobado"
        Factura::whereIn('solicitud_id', $request->solicitudes)->update([
            'situacion' => 'Completado'
        ]);

        return redirect()->route('reposicion.index');
    }

    public function edit(Reposicion $reposicion)
    {
        // Obtener solicitudes disponibles + las ya asociadas a esta reposición
        $solicitudes = Solicitud::where(function ($query) use ($reposicion) {
            $query->where('reposicion_id', $reposicion->id) // ya asociadas a esta reposición
                  ->orWhereHas('facturas', function ($q) {
                      $q->where('situacion', 'Comprobado'); // disponibles para asociar
                  });
        })
        ->get()
        ->filter(function ($solicitud) {
            $sumaFacturas = $solicitud->facturas->sum('importe');
            return abs($sumaFacturas - $solicitud->monto) <= 1;
        });

        return view('reposicion.edit', compact('reposicion', 'solicitudes'));
    }

    public function update(Request $request, Reposicion $reposicion)
    {
        $request->validate([
            'n_revolvencia' => 'required|unique:reposiciones,n_revolvencia,' . $reposicion->id,
            'fecha_reg' => 'required|date',
            'solicitudes' => 'array'
        ]);

        $reposicion->n_revolvencia = $request->input('n_revolvencia');
        $reposicion->fecha_reg = $request->input('fecha_reg');
        $reposicion->save();

        // IDs de solicitudes enviadas desde el formulario
        $idsSolicitudesNuevas = collect($request->solicitudes)->filter()->all();
        
        // IDs actuales asociadas a esta reposición
        $idsSolicitudesActuales = Solicitud::where('reposicion_id', $reposicion->id)->pluck('id')->toArray();

        // Asociar las nuevas (incluye también las que ya estaban, sin problema)
        Solicitud::whereIn('id', $idsSolicitudesNuevas)->update([
            'reposicion_id' => $reposicion->id,
            'estado' => 'Finalizado'
        ]);

        // Cambiar situación de facturas asociadas a esta solicitud a "Comprobado"
        Factura::whereIn('solicitud_id', $idsSolicitudesNuevas)->update([
            'situacion' => 'Completado'
        ]);

        return redirect()->route('reposicion.index')->with('success', 'Reposición actualizada correctamente');
    }


    public function destroy(Reposicion $reposicion)
    {
        if ($reposicion->solicitudes()->exists()) {
            return redirect()->route('reposicion.index')->with('error', 'No se puede eliminar. Tiene solicitudes asociadas.');
        }

        $reposicion->delete();

        return redirect()->route('reposicion.index')->with('success', 'Reposición eliminada.');
    }

    public function detachSolicitud(Reposicion $reposicion, Solicitud $solicitud)
    {
        if ($solicitud->reposicion_id === $reposicion->id) {
            $solicitud->reposicion_id = null;
            $solicitud->estado = 'En proceso';
            $solicitud->save();
        }

        // Cambiar situación de facturas asociadas a esta solicitud a "Comprobado"
        $factura = Factura::where('solicitud_id', $solicitud->id)->get();
        $factura[0]->situacion = 'Comprobado';
        $factura[0]->save();

        return back()->with('status', 'Solicitud desvinculada correctamente.');
    }

    public function exportExcel(Reposicion $reposicion)
    {
        
        return Excel::download(new ReposicionExport($reposicion), 'Fondos_Revolventes_'.$reposicion->n_revolvencia.'.xlsx');
    }

    public function multipleExcel(Request $request)
    {
        if (!$request->has('reposiciones') || count($request->reposiciones) === 0) {
            return back()->with('error', 'Debes seleccionar al menos una reposición para descargar.');
        }

        $ids = $request->input('reposiciones');

        $reposiciones = Reposicion::with('solicitudes.facturas', 'solicitudes.area')->whereIn('id', $ids)->get();

        if ($reposiciones->count() === 1) {
            return $this->exportExcel($reposiciones->first());
        }

        // Si hay más de una, exporta con varias hojas
        return Excel::download(new MultipleRepositionsExport($reposiciones), 'Fondos_Revolventes_Multiples.xlsx');
    }
}
