<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CisternaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlanificacionController;

Route::get('/', function () {
    return redirect()->route('cisterna.index');
});

Route::middleware('auth')->group(function () {

    Route::prefix('planificacion')->name('planificacion.')->group(function () {
        Route::get('/',           [PlanificacionController::class, 'index'])->name('index');
        Route::post('/',          [PlanificacionController::class, 'store'])->name('store');
        Route::get('/{id}/edit',  [PlanificacionController::class, 'edit'])->name('edit');
        Route::patch('/{id}',     [PlanificacionController::class, 'update'])->name('update');
        Route::delete('/{id}',    [PlanificacionController::class, 'destroy'])->name('destroy');
        Route::get('/exportar', [PlanificacionController::class, 'exportar'])->name('exportar');
        Route::delete('/',        [PlanificacionController::class, 'clear'])->name('clear');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:Root,Administrador')->group(function () {
        Route::get('/users', [AdminController::class, 'index'])->name('users');
        Route::post('/users', [AdminController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}/toggle', [AdminController::class, 'toggle'])->name('users.toggle');
        Route::patch('/users/{user}/role', [AdminController::class, 'changeRole'])->name('users.role');
        Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');
    });

    Route::delete('/cisterna/borrar-todas', [CisternaController::class, 'destroyAll'])->name('cisterna.destroyAll');
    Route::get('/cisterna/bulk-upload',   [CisternaController::class, 'bulkUpload'])->name('cisterna.bulk');
    Route::post('/cisterna/bulk-upload',  [CisternaController::class, 'bulkStore'])->name('cisterna.bulk.store');
    Route::get('/cisterna/bulk-confirm',  [CisternaController::class, 'bulkConfirm'])->name('cisterna.bulk.confirm');
    Route::post('/cisterna/bulk-confirm', [CisternaController::class, 'bulkConfirmStore'])->name('cisterna.bulk.confirm.store');
    Route::get('/cisterna/export', [CisternaController::class, 'export'])->name('cisterna.export');
    Route::resource('cisterna', CisternaController::class);
    Route::get('/dashboard', [CisternaController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/cisterna/{cisterna}/consumo', [CisternaController::class, 'updateConsumo'])->name('cisterna.consumo');
    
});

require __DIR__.'/auth.php';