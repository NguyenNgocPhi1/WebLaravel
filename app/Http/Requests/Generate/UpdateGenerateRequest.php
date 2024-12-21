<?php

namespace App\Http\Requests\Generate;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGenerateRequest extends FormRequest
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
            'name' => 'required|unique:generate,name, '.$this->id.'',
            'schema' => 'required',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Bạn chưa nhập vào tên module.',
            'name.unique' => 'Module đã tồn tại.',
            'schema.required' => 'Bạn chưa nhập vào schema của module.',
        ];
    }
}
