<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FacturasExport;

use App\Models\Solicitud;
use App\Models\Factura;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        $solicitudes = Solicitud::with('area')->get()->sortByDesc('id');
        $solicitudId = $request->input('solicitud_id') ?? $solicitudes->first()?->id;

        $facturas = Factura::with('solicitud.area')
            ->where('solicitud_id', $solicitudId)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('facturas.index', compact('facturas', 'solicitudes', 'solicitudId'));
    }

    public function create()
    {
        $solicitudes = Solicitud::with('area')->get();
        return view('facturas.create', compact('solicitudes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_registro' => 'required|date',
            'fecha_factura' => 'required|date',
            'solicitud_id' => 'required|exists:solicitudes,id',
            'factura' => 'required',
            'proveedor' => 'required',
            'concepto_gasto' => 'required',
            'situacion' => 'required',
            'importe' => 'required|numeric',
            'objeto_gasto' => 'required',
            'c_c' => 'required',
            'tipo_factura' => 'required|in:Gasto,Devolucion',
        ]);

        Factura::create($request->all());

        return redirect()->route('factura.index')->with('success', 'Factura registrada correctamente.');
    }

    public function edit(Factura $factura)
    {
        $solicitudes = Solicitud::with('area')->get();
        return view('facturas.edit', compact('factura', 'solicitudes'));
    }

    public function update(Request $request, Factura $factura)
    {
        $request->validate([
            'fecha_registro' => 'required|date',
            'fecha_factura' => 'required|date',
            'solicitud_id' => 'required|exists:solicitudes,id',
            'factura' => 'required',
            'proveedor' => 'required',
            'concepto_gasto' => 'required',
            'situacion' => 'required',
            'importe' => 'required|numeric',
            'objeto_gasto' => 'required',
            'c_c' => 'required',
            'tipo_factura' => 'required|in:Gasto,Devolucion',
        ]);

        $factura->update($request->all());

        return redirect()->route('factura.index')->with('success', 'Factura actualizada correctamente.');
    }

    public function destroy(Factura $factura)
    {
        $factura->delete();
        return back()->with('success', 'Factura eliminada.');
    }


    public function export(Request $request)
    {
        $solicitudId = $request->input('solicitud_id');

        // Aquí puedes aplicar los mismos filtros que en el index
        $facturas = Factura::with('solicitud.area')
        ->where('solicitud_id', $solicitudId)
        ->get();

        // Verificar si hay facturas
        if ($facturas->isEmpty()) {
            return redirect()->back()->with('error', 'No hay facturas registradas para esta solicitud.');
        }

        // Obtén el monto solicitado (por ejemplo, de la primera solicitud asociada)
        $montoSolicitud = optional($facturas->first()->solicitud)->monto ?? 0;

        return Excel::download(new FacturasExport($facturas, $montoSolicitud), 'facturas.xlsx');
    }
}
