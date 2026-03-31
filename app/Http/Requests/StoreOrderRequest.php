<?php

namespace App\Http\Requests;

use App\Models\License;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id'),
                function (string $attribute, mixed $value, Closure $fail): void {
                    $hasAvailableLicense = License::query()
                        ->where('product_id', $value)
                        ->whereNull('user_id')
                        ->exists();

                    if (! $hasAvailableLicense) {
                        $fail('Selected product is out of stock.');
                    }
                },
            ],
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        throw new ValidationException(
            $validator,
            response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}