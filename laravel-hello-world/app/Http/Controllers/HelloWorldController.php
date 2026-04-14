<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;

class HelloWorldController extends Controller
{
    public function getMessage(): JsonResponse
    {
        return response()->json([
            'message' => 'hello world from Laravel'
        ]);
    }
}
