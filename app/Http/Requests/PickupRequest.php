<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PickupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { return true; }

public function rules(): array
{
    return [
        'pickup_code' => 'required|string|exists:ninjavan_data,pickup_code',
    ];
}
}
