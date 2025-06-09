<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserStoreRequest extends FormRequest
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
            'lastname' => ['required', 'string', 'max:200', 'min:2'],
            'firstname' => ['required', 'string', 'max:200', 'min:2'],
            'email' => ['required', 'string', 'email', 'unique:users', 'max:255'],
            'role' => ['nullable', 'string', 'in:user,admin,manager'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lastname.required' => 'Le nom est obligatoire',
            'lastname.min' => 'Le nom doit contenir au moins 2 caractères',
            'firstname.required' => 'Le prénom est obligatoire',
            'firstname.min' => 'Le prénom doit contenir au moins 2 caractères',
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'L\'email doit être une adresse valide',
            'email.unique' => 'Cette adresse email est déjà utilisée',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
            'role.in' => 'Le rôle doit être user, admin ou manager',
        ];
    }
} 