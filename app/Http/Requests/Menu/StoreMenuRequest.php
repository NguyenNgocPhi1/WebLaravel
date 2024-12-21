<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuRequest extends FormRequest
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
            'menu_catalogue_id' => 'gt:0',
            'menu.name' => [
                'required',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'menu_catalogue_id.gt' => 'Bạn chưa chọn vị trí của menu.',
            'menu.name.required' => 'Bạn phải tạo ít nhất 1 menu.',
        ];
    }
}
