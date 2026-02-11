<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TeamMemberController;
use App\Http\Controllers\Api\WorkController;
use Illuminate\Support\Facades\Route;

Route::get('classes', [ClassController::class, 'index']);
Route::get('services', [ServiceController::class, 'index']);
Route::get('services/{id}', [ServiceController::class, 'show']);
Route::get('contacts', [ContactController::class, 'index']);
Route::get('works', [WorkController::class, 'index']);
Route::get('about', [AboutController::class, 'index']);
Route::get('team-members', [TeamMemberController::class, 'index']);