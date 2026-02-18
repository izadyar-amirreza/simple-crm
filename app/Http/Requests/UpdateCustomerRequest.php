<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateCustomerRequest extends FormRequest
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
        $rules = [
            'name'  => ['required','string','max:255'],
            'email' => [
            'nullable','email','max:255',
            Rule::unique('customers','email')->ignore($this->route('customer')->id),
             ],
            'phone' => ['nullable','string','max:50'],
            'notes' => ['nullable','string'],
        ];

        if ($this->user()->hasRole('admin')) {
            $rules['owner_id'] = ['nullable','exists:users,id'];
        }

        return $rules;
    }

}
