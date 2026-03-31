<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\LicenseResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    private OrderService $orderService;

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $license = $this->orderService->purchase(
            userId: (int) $request->integer('user_id'),
            productId: (int) $request->integer('product_id'),
        );

        return response()->json([
            'message' => 'Order created successfully.',
            'data' => new LicenseResource($license),
        ], 201);
    }
}