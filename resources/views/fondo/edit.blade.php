@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Editar Fondo Revolvente</h1>

  <form method="POST" action="{{ route('fondo.update') }}">
    @csrf
    @method('PUT')

    <div class="mb-3">
      <label for="monto" class="form-label">Monto</label>
      <input type="number" name="monto" class="form-control" step="0.01" value="{{ old('monto', $monto) }}" required>
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="{{ route('fondo.index') }}" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
@endsection
