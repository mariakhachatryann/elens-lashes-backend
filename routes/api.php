<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\WorkController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('classes', [ClassController::class, 'index']);
Route::get('services', [ServiceController::class, 'index']);
Route::get('services/{id}', [ServiceController::class, 'show']);
Route::get('contacts', [ContactController::class, 'index']);
Route::get('works', [WorkController::class, 'index']);