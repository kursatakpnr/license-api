<?php

namespace Database\Seeders;

use App\Models\License;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = collect([
            [
                'name' => 'Ahmet Yilmaz',
                'email' => 'ahmet@example.com',
            ],
            [
                'name' => 'Ayse Demir',
                'email' => 'ayse@example.com',
            ],
            [
                'name' => 'Mehmet Kaya',
                'email' => 'mehmet@example.com',
            ],
        ])->map(fn (array $user) => User::query()->create([
            ...$user,
            'password' => Hash::make('password'),
        ]));

        $products = collect([
            [
                'name' => 'Microsoft Office 365',
                'sku' => 'OFFICE-365',
                'price' => 2499.90,
                'license_count' => 8,
            ],
            [
                'name' => 'Windows 11 Pro',
                'sku' => 'WIN11-PRO',
                'price' => 3199.90,
                'license_count' => 4,
            ],
            [
                'name' => 'JetBrains All Products Pack',
                'sku' => 'JETBRAINS-ALL',
                'price' => 4599.90,
                'license_count' => 6,
            ],
        ])->map(fn (array $product) => Product::query()->create([
            'name' => $product['name'],
            'sku' => $product['sku'],
            'price' => $product['price'],
        ]));

        $products->each(function (Product $product, int $index) use ($users): void {
            $licenseCount = match ($product->sku) {
                'OFFICE-365' => 8,
                'WIN11-PRO' => 4,
                'JETBRAINS-ALL' => 6,
                default => 5,
            };

            License::factory()
                ->count($licenseCount)
                ->for($product)
                ->create();

            $assignedCount = match ($product->sku) {
                'OFFICE-365' => 2,
                'WIN11-PRO' => 1,
                'JETBRAINS-ALL' => 3,
                default => 0,
            };

            $availableLicenses = License::query()
                ->where('product_id', $product->id)
                ->whereNull('user_id')
                ->limit($assignedCount)
                ->get();

            $availableLicenses->each(function (License $license, int $licenseIndex) use ($users, $index): void {
                $user = $users[($index + $licenseIndex) % $users->count()];

                $license->update([
                    'user_id' => $user->id,
                ]);
            });
        });
    }
}