<?php

namespace Database\Factories;

use App\Models\License;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<License>
 */
class LicenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => null,
            'license_key' => strtoupper(fake()->bothify('LIC-****-****-****-####')),
        ];
    }
}
