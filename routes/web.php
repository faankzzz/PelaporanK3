<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\K3Controller;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Insiden (semua peran; pembatasan data ada di controller)
    Route::get('/insiden', [IncidentController::class, 'index'])->name('incidents.index');
    Route::get('/insiden/buat', [IncidentController::class, 'create'])->name('incidents.create');
    Route::post('/insiden', [IncidentController::class, 'store'])->name('incidents.store');
    Route::get('/insiden/{incident}', [IncidentController::class, 'show'])->name('incidents.show');
    Route::get('/insiden/{incident}/ubah', [IncidentController::class, 'edit'])->name('incidents.edit');
    Route::put('/insiden/{incident}', [IncidentController::class, 'update'])->name('incidents.update');
    Route::get('/insiden/{incident}/cetak', [IncidentController::class, 'print'])->name('incidents.print');

    // Petugas K3 dan Admin: ekspor laporan
    Route::get('/laporan/ekspor', [IncidentController::class, 'export'])
        ->middleware(RoleMiddleware::class . ':petugas_k3,admin')->name('incidents.export');

    // Petugas K3: verifikasi, investigasi, tindakan, status
    Route::middleware(RoleMiddleware::class . ':petugas_k3')->group(function () {
        Route::post('/insiden/{incident}/verifikasi', [K3Controller::class, 'verify'])->name('k3.verify');
        Route::post('/insiden/{incident}/investigasi', [K3Controller::class, 'investigation'])->name('k3.investigation');
        Route::post('/insiden/{incident}/tindakan', [K3Controller::class, 'storeAction'])->name('k3.actions.store');
        Route::patch('/tindakan/{action}', [K3Controller::class, 'updateAction'])->name('k3.actions.update');
        Route::post('/insiden/{incident}/status', [K3Controller::class, 'status'])->name('k3.status');
    });

    // Admin
    Route::middleware(RoleMiddleware::class . ':admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::get('/master', [MasterController::class, 'index'])->name('master.index');
        Route::post('/master/{tabel}', [MasterController::class, 'store'])->name('master.store');
        Route::delete('/master/{tabel}/{id}', [MasterController::class, 'destroy'])->name('master.destroy');
        Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
    });
});
