@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('error'))
        <div class="alert alert-danger mt-3">
            {{ session('error') }}
        </div>
    @endif
    <h1 class="mb-4">Listado de Reposiciones</h1>
    <a href="{{ route('reposicion.create') }}" class="btn btn-primary mb-3">Crear nueva Reposición</a>

    {{-- ✅ Botón de descarga múltiple --}}
    <form id="descargaMultipleForm" method="GET" action="{{ route('reposicion.descarga.multiple') }}">
        @csrf
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <input type="checkbox" id="selectAll" class="form-check-input me-2">
                <label for="selectAll" class="form-check-label">Seleccionar todo</label>
            </div>
            <button type="submit" class="btn btn-success">Descargar seleccionadas</button>
        </div>

        <div class="accordion" id="reposicionesAccordion">
            @foreach ($reposiciones as $reposicion)
                <div class="accordion-item mb-2">
                    <h2 class="accordion-header" id="heading{{ $reposicion->id }}">
                        <div class="d-flex align-items-center w-100">
                            <input type="checkbox" name="reposiciones[]" value="{{ $reposicion->id }}" class="form-check-input me-2" style="margin-top: 0.25rem;">
                            <button class="accordion-button collapsed flex-grow-1 text-start" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $reposicion->id }}">
                                {{ $reposicion->n_revolvencia }} | Fecha: {{ $reposicion->fecha_reg }}
                            </button>
                        </div>
                    </h2>
                    <div id="collapse{{ $reposicion->id }}" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            {{-- 📥 Botón de descarga individual --}}
                            <a href="{{ route('reposicion.excel', $reposicion->id) }}"
                               class="btn btn-outline-success btn-sm mb-3">
                                Descargar Excel
                            </a>

                            @foreach ($reposicion->solicitudes as $solicitud)
                                <div class="accordion mb-2" id="solicitudAccordion{{ $solicitud->id }}">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingSolicitud{{ $solicitud->id }}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSolicitud{{ $solicitud->id }}">
                                                Solicitud ID: {{ $solicitud->id }} | Concepto: {{ $solicitud->uso }} | Área: {{ $solicitud->area->nombre }} 
                                            </button>
                                        </h2>
                                        <div id="collapseSolicitud{{ $solicitud->id }}" class="accordion-collapse collapse">
                                            <div class="accordion-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Factura</th>
                                                            <th>Proveedor</th>
                                                            <th>CC</th>
                                                            <th>OG</th>
                                                            <th>Importe</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($solicitud->facturas as $factura)
                                                            <tr>
                                                                <td>{{ $factura->factura }}</td>
                                                                <td>{{ $factura->proveedor }}</td>
                                                                <td>{{ $factura->c_c }}</td>
                                                                <td>{{ $factura->o_g }}</td>
                                                                <td>${{ number_format($factura->importe, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="d-flex justify-content-end mt-2">
                                <a href="{{ route('reposicion.edit', $reposicion) }}" class="btn btn-warning me-2">Editar</a>
                                <form action="{{ route('reposicion.destroy', $reposicion) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta reposición?')">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAllCheckbox = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('input[name="reposiciones[]"]');

    selectAllCheckbox.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
    });
});
</script>
@endsection
