<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DemoResourceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'mode' => 'demo',
            'message' => 'Endpoint versionado pronto para contrato real com Sanctum, policies e Resources.',
            'data' => [],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json([
            'mode' => 'demo',
            'id' => $id,
            'data' => null,
        ]);
    }
}
