@extends('layouts.app')
@section('content')
<div class="container">
  <h1>Editar Solicitud</h1>

  <form action="{{ route('solicitud.update', $solicitud->id) }}" method="POST" class="mb-4">
    @csrf
    @method('PUT')

    <div class="row mb-3">
      <div class="col-md-4">
        <label for="fecha" class="form-label">Fecha</label>
        <input name="fecha" id="fecha" type="date" class="form-control" value="{{ $solicitud->fecha }}" required>
      </div>

      <div class="col-md-4">
        <label for="area" class="form-label">Área</label>
        <select name="area" id="area" class="form-select" required>
          <option value="" disabled>Selecciona un área</option>
          @foreach ($areas as $area)
          
            <option value="{{ $area->id }}" {{ $area->id == $solicitud->area_id ? 'selected' : '' }}>
              {{ $area->nombre }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4">
        <label for="personas" class="form-label">Personas</label>

        @php
            // Convertimos el string "Luis\Maria\Pedro" a array
            $personasArray = explode('\\', $solicitud->personas ?? '');

            // Convertimos a array de objetos con clave 'value' para Tagify
            $personasTags = array_map(function($nombre) {
                return ['value' => $nombre];
            }, $personasArray);

            // Codificamos a JSON para poner en el input
            $personasJson = json_encode($personasTags);
        @endphp

        <input name="personas" id="personas" class="form-control" 
              value='{{ $personasJson }}' required>
      </div>

    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label for="uso" class="form-label">Uso</label>
        <input name="uso" id="uso" type="text" class="form-control" value="{{ $solicitud->uso }}" required>
      </div>

      <div class="col-md-6">
        <label for="monto" class="form-label">Monto</label>
        <input name="monto" id="monto" type="number" step="0.01" class="form-control" value="{{ $solicitud->monto }}" required>
      </div>
    </div>

    <button type="submit" class="btn btn-success">Actualizar</button>
    <a href="{{ route('solicitud.index') }}" class="btn btn-secondary">Cancelar</a>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var input = document.querySelector('input[name=personas]');
    new Tagify(input, {
      enforceWhitelist: false,
      delimiters: ",",
      editTags: false,
      dropdown: {
        enabled: 0,
        closeOnSelect: false
      }
    });
  });
</script>

@endsection
