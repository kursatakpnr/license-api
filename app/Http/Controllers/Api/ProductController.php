<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $products = Product::query()
            ->withCount([
                'licenses as available_stock' => fn ($query) => $query->whereNull('user_id'),
            ])
            ->latest('id')
            ->get();

        return ProductResource::collection($products);
    }
}