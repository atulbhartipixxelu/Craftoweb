<?php

use App\Http\Controllers\Docs\DocsController;
use Illuminate\Support\Facades\Route;

/*Route::get('/', function () {
    return view('welcome');
});*/


// Public
Route::get('/docs/login', [DocsController::class, 'showLogin'])->name('docs.login');
Route::post('/docs/login', [DocsController::class, 'login']);

// Protected
Route::middleware('docs.access')->group(function () {
    Route::get('/docs', [DocsController::class, 'index'])->name('docs.index');
    
});

Route::redirect('/', '/docs/login');

Route::post('/docs/logout', [DocsController::class, 'logout'])->name('docs.logout');