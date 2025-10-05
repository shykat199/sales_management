<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:users,id',
            'products'    => 'required|array|min:1',
            'products.*.id'       => 'required|exists:products,id',
            'products.*.qty'      => 'required|numeric|min:1',
            'products.*.price'    => 'required|numeric|min:0.01',
            'products.*.discount' => 'nullable|numeric|min:0|max:100',
            'products.*.total'    => 'required|numeric|min:0.01',
            'grand_total'         => 'required|numeric|min:0.01',
        ];
    }
}
