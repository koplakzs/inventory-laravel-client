<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductStoreRequest extends FormRequest
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
            'name'   => 'required|string|max:255',
            'code'   => 'required|string|max:100|unique:products,code',
            'category_id' => 'required|exists:categories,id',
            'stock'  => 'required|integer|min:0',
            'images' => 'required|array',
            'images.*' => 'file|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}
