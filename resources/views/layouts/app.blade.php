<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Fondo Revolvente</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <!-- En el <head> del archivo layouts/app.blade.php -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('fondo.index') }}">Fondo Revolvente</a>

        <!-- Menú de navegación -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('fondo.*') ? 'active' : '' }}" href="{{ route('fondo.index') }}">Fondo</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('solicitud.*') ? 'active' : '' }}" href="{{ route('solicitud.index') }}">Solicitud de Recurso</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('factura.*') ? 'active' : '' }}" href="{{ route('factura.index') }}">Comprobación Documental</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reposicion.*') ? 'active' : '' }}" href="{{ route('reposicion.index') }}">Repoosiciones</a>
                </li>
            </ul>

            <span class="navbar-text text-white">
                Monto actual: ${{ number_format($fondo_monto ?? 0, 2) }}
            </span>
        </div>
    </div>
</nav>

<main class="mb-5">
    @yield('content')
</main>

@yield('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
</body>
</html>
