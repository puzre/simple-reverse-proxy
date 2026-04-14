<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\HelloWorldController;
use Illuminate\Support\Facades\Route;

Route::get('/hello-world', [HelloWorldController::class, 'getMessage']);
