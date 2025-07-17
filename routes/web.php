<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FondoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\AreaController;

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
