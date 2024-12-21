<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store{Module}Request extends FormRequest
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
            'name' => 'required',
            'canonical' => 'required|unique:routers',
            
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập ô tiêu đề.',
            'canonical.required' => 'Bạn chưa nhập ô đường dẫn.',
            'canonical.unique' => 'Đường dẫn đã tồn tại, hãy chọn đường dẫn khác.'
        ];
    }
}