<?php

namespace App\Http\Controllers;

use App\Models\Reposicion;
use App\Models\Solicitud;
use Illuminate\Http\Request;

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

        Solicitud::whereIn('id', $request->solicitudes)->update(['reposicion_id' => $reposicion->id]);

        return redirect()->route('reposicion.index');
    }

    public function edit(Reposicion $reposicion)
    {
        // Obtener solicitudes disponibles + las ya asociadas a esta reposición
        $solicitudes = Solicitud::where(function ($query) use ($reposicion) {
            $query->whereNull('reposicion_id')
                ->orWhere('reposicion_id', $reposicion->id);
        })
        ->whereDoesntHave('facturas', function ($q) {
            $q->where('situacion', '!=', 'Comprobado');
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

        // Asignar los campos manualmente
        $reposicion->n_revolvencia = $request->input('n_revolvencia');
        $reposicion->fecha_reg = $request->input('fecha_reg');

        // Guardar cambios
        $reposicion->save();

        // Desvincular solicitudes anteriores (dejar reposicion_id en null)
        $solicitudesPrevias = Solicitud::where('reposicion_id', $reposicion->id)->get();
        foreach ($solicitudesPrevias as $sol) {
            $sol->reposicion_id = null;
            $sol->save();
        }

        // Vincular nuevas solicitudes
        $nuevasSolicitudes = Solicitud::whereIn('id', $request->solicitudes)->get();
        foreach ($nuevasSolicitudes as $sol) {
            $sol->reposicion_id = $reposicion->id;
            $sol->save();
        }

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
            $solicitud->save();
        }

        return back()->with('status', 'Solicitud desvinculada correctamente.');
    }

    public function exportExcel(Reposicion $reposicion)
    {
        
        return Excel::download(new ReposicionExport($reposicion), 'Fondos_Revolventes_'.$reposicion->n_revolvencia.'.xlsx');
    }
}
