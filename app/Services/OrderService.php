<?php

namespace App\Services;

use App\Jobs\SendLicenseEmailJob;
use App\Models\License;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderService
{
    public function purchase(int $userId, int $productId): License
    {
        $license = DB::transaction(function () use ($userId, $productId) {
            $license = License::query()
                ->where('product_id', $productId)
                ->whereNull('user_id')
                ->lockForUpdate()
                ->first();

            if (! $license) {
                throw new HttpException(422, 'Selected product is out of stock.');
            }

            $license->update([
                'user_id' => $userId,
            ]);

            return $license->fresh(['product', 'user']);
        });

        SendLicenseEmailJob::dispatch($license);

        return $license;
    }
}