<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'name'     => 'required|string|max:100',
            'email'    => 'required|email:rfc,dns|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&^()\-_=+])[A-Za-z\d@$!%*#?&^()\-_=+]{8,}$/',
            ],
            'date_birth' => 'required|date|before:today',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'       => 'Nome',
            'email'      => 'E-mail',
            'password'   => 'Senha',
            'date_birth' => 'Data de Nascimento',
        ];
    }

    public function messages(): array
    {
        return [
            'password.confirmed' => 'A confirmação da senha não corresponde.',
            'password.regex'     => 'A senha deve conter pelo menos uma letra maiúscula, uma minúscula, um número e um caractere especial.',
            'email.email' => 'O e-mail informado não é válido ou o domínio não existe.',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Nome completo do usuário (máximo 100 caracteres).',
                'example'     => 'João da Silva',
            ],
            'email' => [
                'description' => 'E-mail válido e único.',
                'example'     => 'joao@email.com',
            ],
            'password' => [
                'description' => 'Senha com no mínimo 8 caracteres, contendo ao menos uma letra maiúscula, uma minúscula, um número e um caractere especial.',
                'example'     => 'SenhaForte@123',
            ],
            'password_confirmation' => [
                'description' => 'Confirmação da senha (deve ser igual à senha).',
                'example'     => 'SenhaForte@123',
            ],
            'date_birth' => [
                'description' => 'Data de nascimento no formato YYYY-MM-DD. Deve ser anterior à data atual.',
                'example'     => '1990-05-21',
            ],
        ];
    }
}
