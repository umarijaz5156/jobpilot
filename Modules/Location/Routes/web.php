<?php

use Illuminate\Support\Facades\Route;
use Modules\Location\Http\Controllers\CountryController;

Route::middleware(['auth:admin', 'set_lang'])->group(function () {
    // Country CRUD
    Route::prefix('admin/country')->group(function () {
        Route::get('/', [CountryController::class, 'index'])->name('module.country.index');
        Route::get('create', [CountryController::class, 'create'])->name('module.country.create');
        Route::post('store', [CountryController::class, 'store'])->name('module.country.store');
        Route::get('edit/{country}', [CountryController::class, 'edit'])->name('module.country.edit');
        Route::put('update/{country}', [CountryController::class, 'update'])->name('module.country.update');
        Route::delete('delete/{country}', [CountryController::class, 'destroy'])->name('module.country.delete');
        Route::delete('multiple/delete', [CountryController::class, 'multipleDestroy'])->name('module.country.multiple.delete');
        Route::post('/set/app/country', [CountryController::class, 'setAppCountry'])->name('module.set.app.country');
    });
});
