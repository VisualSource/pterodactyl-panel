<?php

use Illuminate\Support\Facades\Route;
use Pterodactyl\Http\Controllers\Admin;

Route::get('/', [Admin\BaseController::class, 'index'])->name('admin.index')->fallback();
Route::get('/{react}', [Admin\BaseController::class, 'index'])->where('react', '.+');

/*
|--------------------------------------------------------------------------
| Domain Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /admin/domains
|
*/
Route::group(['prefix' => 'domains'], function () {
    Route::get('/', [Admin\Domains\DomainController::class, 'index'])->name('admin.domains');

    Route::post('/new', [Admin\Domains\DomainController::class, 'store'])->name('admin.domains.new');
    Route::patch('/{domain:id}',[Admin\Domains\DomainController::class,'update'])->name('admin.domains.patch');
    Route::delete('/{domain:id}', [Admin\Domains\DomainController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Domain Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /admin/domains
|
*/
Route::group(['prefix' => 'ports'], function () {
    Route::get('/', [Admin\PortController::class, 'index'])->name('admin.ports');
    Route::get('/new', [Admin\PortController::class, 'create'])->name('admin.ports.new');
    Route::get('/view/{port:id}', [Admin\PortController::class, 'view'])->name('admin.ports.view');

    Route::post('/new', [Admin\PortController::class, 'store'])->name('admin.ports.create');
    Route::patch('/{port:id}',[Admin\PortController::class,'update']);
    Route::delete('/{port:id}', [Admin\PortController::class, 'destroy']);
});