<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\BeritaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/websites/export', [WebsiteController::class, 'export'])->name('websites.export');
    Route::resource('websites', WebsiteController::class);
    Route::resource('beritas', BeritaController::class);
    Route::post('beritas/upload-image', [BeritaController::class, 'uploadImage'])->name('beritas.upload_image');
});

require __DIR__ . '/auth.php';
