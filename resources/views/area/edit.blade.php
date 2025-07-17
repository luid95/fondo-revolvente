@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Área</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('area.update', $area->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Nombre:</label>
            <input type="text" name="nombre" value="{{ old('nombre', $area->nombre) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Código:</label>
            <input type="text" name="codigo" value="{{ old('codigo', $area->codigo) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Siglas:</label>
            <input type="text" name="siglas" value="{{ old('siglas', $area->siglas) }}" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('fondo.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
