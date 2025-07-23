@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Reposición</h1>

    {{-- Formulario para actualizar datos de la reposición --}}
    <form action="{{ route('reposicion.update', $reposicion) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="n_revolvencia" class="form-label">N° Revolvencia</label>
            <input type="text" name="n_revolvencia" id="n_revolvencia" class="form-control @error('n_revolvencia') is-invalid @enderror" value="{{ old('n_revolvencia', $reposicion->n_revolvencia) }}" required>
            @error('n_revolvencia')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="fecha_reg" class="form-label">Fecha de Registro</label>
            <input type="date" name="fecha_reg" id="fecha_reg" class="form-control @error('fecha_reg') is-invalid @enderror" value="{{ old('fecha_reg', $reposicion->fecha_reg) }}" required>
            @error('fecha_reg')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Select para agregar nuevas solicitudes disponibles --}}
        <div class="mb-3">
            <label class="form-label">Agregar Solicitudes</label>
            <div class="@error('solicitudes') is-invalid @enderror"  style="max-height: 200px; overflow-y: auto; border: 1px solid #ced4da; padding: 10px; border-radius: .375rem;">
                @foreach ($solicitudes->where('reposicion_id', null) as $solicitud)
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="solicitudes[]"
                            value="{{ $solicitud->id }}"
                            id="solicitud{{ $solicitud->id }}"
                            {{ in_array($solicitud->id, old('solicitudes', [])) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="solicitud{{ $solicitud->id }}">
                            Solicitud #{{ $solicitud->id }} - ${{ number_format($solicitud->monto, 2) }}
                        </label>
                    </div>
                @endforeach
            </div>
            <small class="text-muted">Selecciona una o más solicitudes para agregar a esta reposición</small>
            @error('solicitudes')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>


        <div class="d-flex justify-content-between">
            <a href="{{ route('reposicion.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
    </form>

    {{-- Tabla de solicitudes ya asociadas con opción de quitar --}}
    <hr class="my-4">
    <h4>Solicitudes Asociadas</h4>

    @if ($solicitudes->where('reposicion_id', $reposicion->id)->isEmpty())
        <p>No hay solicitudes asociadas a esta reposición.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Monto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($solicitudes->where('reposicion_id', $reposicion->id) as $solicitud)
                    <tr>
                        <td>{{ $solicitud->id }}</td>
                        <td>${{ number_format($solicitud->monto, 2) }}</td>
                        <td>
                            <form action="{{ route('reposicion.detachSolicitud', [$reposicion, $solicitud]) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas quitar esta solicitud?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger btn-sm">Quitar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
