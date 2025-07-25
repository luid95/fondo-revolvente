@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Comprobación Documental</h2>

    <form method="GET" class="mb-3">
        <label for="solicitud_id">Filtrar por Solicitud:</label>
        <select name="solicitud_id" id="solicitud_id" class="form-select" onchange="this.form.submit()">
            @foreach ($solicitudes as $sol)
                <option value="{{ $sol->id }}" {{ $sol->id == $solicitudId ? 'selected' : '' }}>
                    Solicitud #{{ $sol->id }} - Área: {{ $sol->area->nombre ?? 'N/A' }} - Fecha: {{ $sol->fecha}} - Uso: {{ $sol->uso}} - Monto: ${{ $sol->monto}}
                </option>
            @endforeach
        </select>
    </form>

    <a href="{{ route('factura.create') }}" class="btn btn-primary mb-3">Agregar Factura</a>

    <!-- <a href="{{ route('factura.exportar', ['solicitud_id' => request('solicitud_id')]) }}" class="btn btn-outline-success mb-3">
        Exportar a Excel
    </a> -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Concepto de gasto</th>
                <th>Tipo de Factura</th>
                <th>Situación</th>
                <th>Área</th>
                <th>Proveedor</th>
                <th>Importe</th>
                <th>O.G.</th>
                <th>C.C.</th>
                <th>Fecha Registro</th>
                <th>Fecha Factura</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($facturas as $factura)
                <tr>
                    <td>{{ $factura->concepto_gasto }}</td>
                    <td>{{ $factura->tipo_factura }}</td>
                    <td>
                        @foreach(explode(',', $factura->situacion) as $sit)
                            @php
                                $sit = trim($sit);
                                $color = match($sit) {
                                    'Comprobado', 'Completado' => 'success',
                                    'Devolucion de Recursos', 'Tramitado' => 'primary',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $color }}">{{ $sit }}</span>
                        @endforeach
                    </td>
                    <td>{{ $factura->solicitud->area->nombre ?? 'N/A' }}</td>
                    <td>{{ $factura->proveedor }}</td>
                    <td>${{ number_format($factura->importe, 2) }}</td>
                    <td>{{ $factura->objeto_gasto ?? 'N/A' }}</td>
                    <td>{{ $factura->c_c ?? 'N/A' }}</td>
                    <td>{{ $factura->fecha_registro }}</td>
                    <td>{{ $factura->fecha_factura }}</td>
                    <td>
                        
                        @if ($factura->situacion !== 'Completado')
                            <a href="{{ route('factura.edit', $factura) }}" class="btn btn-sm btn-warning">Editar</a>
                            <form action="{{ route('factura.destroy', $factura) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta factura?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                        @else
                            <span class="text-muted">Asignado</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">No hay facturas para esta solicitud.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-end">Total Importe:</th>
                <th>${{ number_format($facturas->sum('importe'), 2) }}</th>
            </tr>
        </tfoot>
    </table>

    @php
        $montoSolicitud = $facturas->first()?->solicitud?->monto ?? 0;
        $totalFacturas = $facturas->sum('importe');
        $diferencia = $montoSolicitud - $totalFacturas;
    @endphp

    <div class="mt-4">
        <p><strong>Monto del Recurso Solicitado:</strong> ${{ number_format($montoSolicitud, 2) }}</p>
        <p><strong>Diferencia:</strong> ${{ number_format($diferencia, 2) }}</p>

        @if($diferencia > 1)
            <div class="alert alert-warning">Falta recurso por comprobar</div>
        @elseif(abs($diferencia) <= 1)
            <div class="alert alert-success">Solicitud comprobada exitosamente</div>
        @else
            <div class="alert alert-danger">Factura sobrepasa el total del recurso solicitado</div>
        @endif
    </div>

</div>
<script>
    $(document).ready(function() {
        $('#solicitud_id').select2({
            placeholder: 'Busca una solicitud...',
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection
