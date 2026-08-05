<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReferenceController;

Route::apiResource('references', ReferenceController::class);
