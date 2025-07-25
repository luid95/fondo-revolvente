@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Agregar nueva factura</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>¡Ups!</strong> Hubo algunos problemas con tus datos:<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('factura.store') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="solicitud_id" class="form-label">Solicitud</label>
                <select name="solicitud_id" class="form-select" required>
                    <option value="">Selecciona una solicitud</option>
                    @foreach ($solicitudes as $sol)
                        <option value="{{ $sol->id }}" {{ old('solicitud_id') == $sol->id ? 'selected' : '' }}>
                            #{{ $sol->id }} - {{ $sol->area->nombre ?? 'Sin área' }} - Fecha: {{ $sol->fecha}} - Uso: {{ $sol->uso}} - Monto: ${{ $sol->monto}}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="fecha_registro" class="form-label">Fecha de registro</label>
                <input type="date" name="fecha_registro" class="form-control" value="{{ old('fecha_registro') }}" required>
            </div>
            <div class="col-md-4">
                <label for="fecha_factura" class="form-label">Fecha de factura</label>
                <input type="date" name="fecha_factura" class="form-control" value="{{ old('fecha_factura') }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="factura" class="form-label">Número de factura</label>
                <input type="text" name="factura" class="form-control" value="{{ old('factura') }}" required>
            </div>
            <div class="col-md-6">
                <label for="proveedor" class="form-label">Proveedor</label>
                <input type="text" name="proveedor" class="form-control" value="{{ old('proveedor') }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="concepto_gasto" class="form-label">Concepto del gasto</label>
            <textarea name="concepto_gasto" class="form-control" rows="2" required>{{ old('concepto_gasto') }}</textarea>
        </div>

        <div class="row mb-3">
            <div class="col-md-3">
                <label for="importe" class="form-label">Importe</label>
                <input type="number" step="0.01" name="importe" class="form-control" value="{{ old('importe') }}" required>
            </div>

            <div class="col-md-3">
                <label for="tipo_factura" class="form-label">Tipo de factura</label>
                <select name="tipo_factura" class="form-select" required>
                    <option value="Gasto" {{ old('tipo_factura') == 'Gasto' ? 'selected' : '' }}>Gasto</option>
                    <option value="Devolucion" {{ old('tipo_factura') == 'Devolucion' ? 'selected' : '' }}>Devolución</option>
                </select>

            </div>

            <div class="col-md-3">
                <label for="situacion" class="form-label">Situación</label>
                <select name="situacion" id="situacion" class="form-select" required>
                    @php
                        $situaciones = ['Comprobado', 'Devolucion de Recursos', 'Tramitado'];
                    @endphp
                    @foreach ($situaciones as $s)
                        <option value="{{ $s }}" {{ old('situacion', 'Comprobado') == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="objeto_gasto" class="form-label">Objeto Gasto</label>
                <input type="text" name="objeto_gasto" class="form-control" value="{{ old('objeto_gasto') }}">
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <label for="c_c" class="form-label">C.C.</label>
                <input type="text" name="c_c" class="form-control" value="{{ old('c_c') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-success">Guardar factura</button>
        <a href="{{ route('factura.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
