@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Crear Reposición</h2>
    <form method="POST" action="{{ route('reposicion.store') }}">
        @csrf
        <div class="mb-3">
            <label>Nombre de Reposición</label>
            <input type="text" name="nombre_rep" class="form-control @error('nombre_rep') is-invalid @enderror" value="{{ old('nombre_rep') }}" required>
            @error('nombre_rep')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label>Número de Revolvencia</label>
            <input type="text" name="n_revolvencia" class="form-control @error('n_revolvencia') is-invalid @enderror" value="{{ old('n_revolvencia') }}" required>
            @error('n_revolvencia')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label>Fecha de Registro</label>
            <input type="date" name="fecha_reg" class="form-control @error('fecha_reg') is-invalid @enderror" value="{{ old('fecha_reg') }}" required>
            @error('fecha_reg')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Solicitudes disponibles</label>
            <div class="@error('solicitudes') is-invalid @enderror" style="max-height: 200px; overflow-y: auto; border: 1px solid #ced4da; padding: 10px; border-radius: .375rem;">
                @forelse ($solicitudes as $sol)
                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            name="solicitudes[]" 
                            value="{{ $sol->id }}"
                            id="solicitud{{ $sol->id }}"
                            {{ (is_array(old('solicitudes')) && in_array($sol->id, old('solicitudes'))) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="solicitud{{ $sol->id }}">
                            Solicitud #{{ $sol->id }} - Monto: ${{ number_format($sol->monto, 2) }}
                        </label>
                    </div>
                @empty
                    <p class="text-muted">No hay solicitudes disponibles</p>
                @endforelse
            </div>
            @error('solicitudes')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>


        <button class="btn btn-success">Guardar</button>
    </form>
</div>
@endsection
