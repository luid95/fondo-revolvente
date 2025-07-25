@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registrar Nueva Área</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('area.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Nombre<span class="text-danger">*</span>:</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Código<span class="text-danger">*</span>:</label>
            <input type="text" name="codigo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Siglas<span class="text-danger">*</span>:</label>
            <input type="text" name="siglas" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('fondo.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
