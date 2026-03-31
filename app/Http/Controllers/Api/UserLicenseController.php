<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LicenseResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserLicenseController extends Controller
{
    public function index(User $user): AnonymousResourceCollection
    {
        $licenses = $user->licenses()
            ->with('product:id,name,sku')
            ->latest('id')
            ->get();

        return LicenseResource::collection($licenses);
    }
}