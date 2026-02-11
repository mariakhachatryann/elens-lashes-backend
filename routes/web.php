<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\WorkController;


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');    
});

Route::get('login', function () {
    return redirect()->route('admin.login');
})->name('login');

Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    Route::get('/', function() { return redirect()->route('admin.services.index'); });
    Route::resource('services', ServiceController::class)->except(['create']);
    Route::resource('classes', ClassController::class)->except(['create']);
    Route::resource('contacts', ContactController::class)->except(['create']);
    Route::resource('works', WorkController::class)->except(['create']);
    Route::get('about', [AboutController::class, 'index'])->name('about.index');
    Route::post('about', [AboutController::class, 'store'])->name('about.store');
    Route::resource('team-members', TeamMemberController::class)->except(['create', 'show']);
});