<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // adjust for auth later
    }

    public function rules(): array
    {
        return [
            'name'  => ['required','string','min:2','max:255'],
            'email' => ['nullable','email:rfc,dns','max:255','unique:contacts,email'],
            'phone' => ['nullable','regex:/^\d{7,15}$/','unique:contacts,phone'],
            'gender'=> ['nullable','in:male,female,other'],
            'profile_image' => ['nullable','image','max:2048'],
            'additional_file' => ['nullable','file','max:5120'],
        ];
    }
}
