<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerCatalogueRequest extends FormRequest
{
    /**
     * Determine if the Customer is authorized to make this request.
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
            'name' => 'required|string|',    
        ];
    }
    public function messages(): array
    {
        return [
            
            'name.required' => 'Bạn chưa nhập nhóm thành viên.', 
            'name.string' => 'Nhóm thành viên phải là dạng ký tự.', 
            
        ];
    }
}
