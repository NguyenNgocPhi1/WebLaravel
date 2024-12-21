<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserCatalogueRequest extends FormRequest
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
            'name' => 'required|string|',    
        ];
    }
    public function messages(): array
    {
        // return [
        //     'email.required' => 'Bạn chưa nhập vào email.',
        //     'email.email' => 'Email chưa đúng định dạng. Ví dụ: abc@gmail.com',
        //     'password.required' => 'Bạn chưa nhập vào password.',
        // ];
        
        return [
            
            'name.required' => 'Bạn chưa nhập nhóm thành viên.', 
            'name.string' => 'Nhóm thành viên phải là dạng ký tự.', 
            
        ];
    }
}
