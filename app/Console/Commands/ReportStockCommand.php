<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class ReportStockCommand extends Command
{
    protected $signature = 'report:stock';

    protected $description = 'List products with available stock below 5';

    public function handle(): int
    {
        $products = Product::query()
            ->withCount([
                'licenses as available_stock' => function ($query) {
                    $query->whereNull('user_id');
                },
            ])
            ->get()
            ->filter(function (Product $product) {
                return $product->available_stock < 5;
            });

        if ($products->isEmpty()) {
            $this->info('No products with low stock.');
            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Name', 'SKU', 'Available Stock'],
            $products->map(function (Product $product) {
                return [
                    $product->id,
                    $product->name,
                    $product->sku,
                    $product->available_stock,
                ];
            })->values()->all()
        );

        return self::SUCCESS;
    }
}