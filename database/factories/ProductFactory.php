<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Microsoft Office 365',
                'Windows 11 Pro',
                'JetBrains All Products Pack',
                'Adobe Creative Cloud',
                'ESET Internet Security',
            ]),
            'sku' => strtoupper(fake()->unique()->bothify('LIC-###??')),
            'price' => fake()->randomFloat(2, 199, 4999),
        ];
    }
}