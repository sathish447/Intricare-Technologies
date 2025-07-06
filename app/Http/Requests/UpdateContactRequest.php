<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $contactId = $this->route('contact')->id ?? null;
        return [
            'name'  => ['required','string','max:255'],
            'email' => ['nullable','email:rfc,dns','max:255','unique:contacts,email,'.$contactId],
            'phone' => ['nullable','regex:/^\d{7,15}$/','unique:contacts,phone,'.$contactId],
            'gender'=> ['nullable','in:male,female,other'],
            'profile_image' => ['nullable','image','max:2048'],
            'additional_file' => ['nullable','file','max:5120'],
        ];
    }
}
