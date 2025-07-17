<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FondoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\FacturaController;

Route::get('/', [FondoController::class, 'index'])->name('fondo.index');
Route::get('/fondo/edit', [FondoController::class, 'edit'])->name('fondo.edit');
Route::put('/fondo', [FondoController::class, 'update'])->name('fondo.update');

Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitud.index');
Route::post('/solicitudes', [SolicitudController::class, 'store'])->name('solicitud.store');
Route::get('/solicitudes/{solicitud}/edit', [SolicitudController::class, 'edit'])->name('solicitud.edit');
Route::put('/solicitudes/{solicitud}', [SolicitudController::class, 'update'])->name('solicitud.update');
Route::delete('/solicitudes/{solicitud}', [SolicitudController::class, 'destroy'])->name('solicitud.destroy');

Route::get('/areas', [AreaController::class, 'create'])->name('area.create');
Route::put('/areas/{id}/restaurar', [AreaController::class, 'restore'])->name('areas.restore');
Route::post('/areas', [AreaController::class, 'store'])->name('area.store');
Route::get('/areas/{area}/edit', [AreaController::class, 'edit'])->name('area.edit');
Route::put('/areas/{area}', [AreaController::class, 'update'])->name('area.update');
Route::delete('/areas/{area}', [AreaController::class, 'destroy'])->name('area.destroy');

Route::get('/facturas', [FacturaController::class, 'index'])->name('factura.index'); // Lista filtrada por solicitud
Route::get('/facturas/create', [FacturaController::class, 'create'])->name('factura.create'); // Formulario nueva factura
Route::post('/facturas', [FacturaController::class, 'store'])->name('factura.store'); // Guardar nueva factura
Route::get('/facturas/{factura}/edit', [FacturaController::class, 'edit'])->name('factura.edit'); // Editar factura
Route::put('/facturas/{factura}', [FacturaController::class, 'update'])->name('factura.update'); // Actualizar factura
Route::delete('/facturas/{factura}', [FacturaController::class, 'destroy'])->name('factura.destroy'); // Eliminar factura
Route::get('/facturas/exportar', [FacturaController::class, 'export'])->name('factura.exportar'); //Export de excel