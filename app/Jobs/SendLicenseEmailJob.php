<?php

namespace App\Jobs;

use App\Models\License;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendLicenseEmailJob implements ShouldQueue
{
    use Queueable;

    private License $license;

    public function __construct(License $license)
    {
        $this->license = $license;
    }

    public function handle(): void
    {
        $license = License::query()
            ->with(['product', 'user'])
            ->find($this->license->id);

        if (! $license || ! $license->user) {
            return;
        }

        Log::info('License delivered to user.', [
            'user_id' => $license->user->id,
            'user_email' => $license->user->email,
            'product_id' => $license->product->id,
            'product_name' => $license->product->name,
            'license_id' => $license->id,
            'license_key' => $license->license_key,
        ]);
    }
}