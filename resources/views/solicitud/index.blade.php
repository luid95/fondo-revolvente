@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Listado de Solicitudes</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <form action="{{ route('solicitud.store') }}" method="POST" class="mb-4">
        @csrf
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="id" class="form-label">ID</label>
                <input name="id" id="id" type="text" class="form-control" placeholder="ID" required>
            </div>

            <div class="col-md-4">
                <label for="fecha" class="form-label">Fecha</label>
                <input name="fecha" id="fecha" type="date" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label for="area" class="form-label">Área</label>
                <select name="area" id="area" class="form-select" required>
                    <option value="" disabled selected>Selecciona un área</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="personas" class="form-label">Personas</label>
                <input name="personas" id="personas" class="form-control" placeholder="Agrega nombres separados" required>
            </div>

            <div class="col-md-4">
                <label for="uso" class="form-label">Uso</label>
                <input name="uso" id="uso" type="text" class="form-control" placeholder="Uso" required>
            </div>

            <div class="col-md-4">
                <label for="monto" class="form-label">Monto</label>
                <input name="monto" id="monto" type="number" step="0.01" class="form-control" placeholder="Monto" required>
            </div>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-success">Agregar</button>
        </div>
    </form>



    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Área</th>
                    <th>Personas</th>
                    <th>Uso</th>
                    <th>Importe Entregado</th>
                    <th>Saldo Restante</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $saldoRestante = $monto;
                @endphp

                @forelse($solicitudes as $s)
                    @php
                        $saldoRestante -= $s->monto;
                    @endphp
                    <tr>
                        <td>{{ $s->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($s->fecha)->format('Y-m-d') }}</td>
                        <td>{{ $s->area ? $s->area->nombre : 'N/A' }}</td>
                        <td>
                            @foreach(explode('\\', $s->personas) as $nombre)
                                <span class="badge bg-primary me-1">{{ $nombre }}</span>
                            @endforeach
                        </td>
                        <td>{{ $s->uso }}</td>
                        <td>${{ number_format($s->monto, 2) }}</td>
                        <td>${{ number_format($saldoRestante, 2) }}</td>
                        <td>{{ ucfirst($s->estado) }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('solicitud.edit', $s->id) }}" class="btn btn-warning btn-sm">Editar</a>

                            <form action="{{ route('solicitud.destroy', $s->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta solicitud?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No hay solicitudes registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Controles de paginación manual -->
    <nav class="d-flex justify-content-center mt-3">
        <ul class="pagination">
            <!-- Botón anterior -->
            <li class="page-item {{ $solicitudes->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $solicitudes->previousPageUrl() }}" aria-label="Anterior">
                    &laquo;
                </a>
            </li>

            <!-- Números de página -->
            @for ($i = 1; $i <= $solicitudes->lastPage(); $i++)
                <li class="page-item {{ $solicitudes->currentPage() == $i ? 'active' : '' }}">
                    <a class="page-link" href="{{ $solicitudes->url($i) }}">{{ $i }}</a>
                </li>
            @endfor

            <!-- Botón siguiente -->
            <li class="page-item {{ !$solicitudes->hasMorePages() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $solicitudes->nextPageUrl() }}" aria-label="Siguiente">
                    &raquo;
                </a>
            </li>
        </ul>
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.querySelector('input[name=personas]');

        new Tagify(input, {
            enforceWhitelist: false, // Permite ingresar nombres nuevos
            delimiters: ",",         // Separador de tags (coma)
            editTags: false,         // Desactiva la edición del tag al hacer clic
            dropdown: {
                enabled: 0,          // Mostrar sugerencias al escribir (puedes modificar a 1 o más)
                closeOnSelect: false
            }
        });
    });
</script>



@endsection