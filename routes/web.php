<?php

use Illuminate\Support\Facades\Route;
use LicenseClient\Http\Controllers\LicenseController;


Route::middleware('license.check')->group(function () {
    Route::get('/request-code', [LicenseController::class, 'requestCode']);
    Route::get('/activate', [LicenseController::class, 'activateForm']);
    Route::post('/activate', [LicenseController::class, 'activate']);
    Route::get('/import/upload-key', [LicenseController::class, 'formKeyPublic']);
    Route::post('/uploadkey', [LicenseController::class, 'uploadKey']);

    //Index aqui
   // Route::get('/', [LicenseController::class, 'index'])->name('index')->middleware('license.check');

});