@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Fondo Revolvente</h1>

  {{-- Mensajes de alerta --}}
  @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
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

  @if(session('warning') && session('restore_area_id'))
      <div class="alert alert-warning alert-dismissible fade show d-flex justify-content-between align-items-center" role="alert">
          <div>
              {{ session('warning') }} (ID: {{ session('restore_area_id') }})
          </div>
          <div class="d-flex align-items-center ms-3">
              <form action="{{ route('areas.restore', session('restore_area_id')) }}" method="POST" class="me-2">
                  @csrf
                  @method('PUT')
                  <button class="btn btn-sm btn-outline-warning">Recuperar Área</button>
              </form>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
          </div>
      </div>
  @endif

  <p><strong>Monto actual:</strong> ${{ number_format($monto, 2) }}</p>
  <a href="{{ route('fondo.edit') }}" class="btn btn-primary mb-4">Editar monto</a>

  {{-- Accordion para Áreas --}}
  <div class="accordion mb-3" id="accordionAreas">
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingAreas">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAreas" aria-expanded="true" aria-controls="collapseAreas">
          Áreas Registradas
        </button>
      </h2>
      <div id="collapseAreas" class="accordion-collapse collapse show" aria-labelledby="headingAreas" data-bs-parent="#accordionAreas">
        <div class="accordion-body">
          <a href="{{ route('area.create') }}" class="btn btn-success mb-3">Nueva Área</a>
          <form method="GET" action="{{ route('fondo.index') }}" class="mb-4 d-flex gap-2">
              <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o ID" value="{{ request('search') }}">
              <button type="submit" class="btn btn-secondary">Buscar</button>
          </form>

          <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Código</th>
                  <th>Siglas</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($areas as $area)
                  <tr>
                    <td>{{ $area->id }}</td>
                    <td>{{ $area->nombre }}</td>
                    <td>{{ $area->codigo }}</td>
                    <td>{{ $area->siglas }}</td>
                    <td class="text-nowrap">
                      <a href="{{ route('area.edit', $area->id) }}" class="btn btn-warning btn-sm">Editar</a>
                      <form action="{{ route('area.destroy', $area->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta área?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center">No hay áreas registradas.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <nav class="d-flex justify-content-center mt-3">
            <ul class="pagination">
              <li class="page-item {{ $areas->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $areas->appends(request()->only('search'))->previousPageUrl() }}">&laquo;</a>
              </li>
              @for ($i = 1; $i <= $areas->lastPage(); $i++)
                <li class="page-item {{ $areas->currentPage() == $i ? 'active' : '' }}">
                  <a class="page-link" href="{{ $areas->appends(request()->only('search'))->url($i) }}">{{ $i }}</a>
                </li>
              @endfor
              <li class="page-item {{ !$areas->hasMorePages() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $areas->appends(request()->only('search'))->nextPageUrl() }}">&raquo;</a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
