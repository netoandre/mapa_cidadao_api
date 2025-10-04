<?php

namespace App\Http\Requests;

use App\DTOs\InactiveOcurrenceRequestDTO;
use App\Enums\TypeOcurrenceClosure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InactiveOcurrenceRequest extends FormRequest
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
            'type_closure' => [
                'required',
                Rule::in(array_map(fn ($value) => $value->value, TypeOcurrenceClosure::cases())),
            ],
            'solution_description' => [
                'string',
                'max:500',
                Rule::requiredIf(fn () => in_array($this->input('type_closure'), [
                    TypeOcurrenceClosure::RESOLVED->value,
                    TypeOcurrenceClosure::OTHER->value,
                ])),
            ],
            'resolution_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
        ];
    }

    public function getDto(): InactiveOcurrenceRequestDTO
    {

        return new InactiveOcurrenceRequestDTO($this->validated());
    }

    /**
     * Get the body parameters for API documentation.
     */
    public function bodyParameters(): array
    {
        return [
            'type_closure' => [
                'description' => 'Tipo de encerramento de ocorrência.',
                'example'     => 'mistake',
            ],
            'solution_description' => [
                'description' => 'Descrição da solução aplicada. Obrigatório se type_closure for resolved ou other.',
                'example'     => 'Problema resolvido pela companhia',

            ],
            'resolution_date' => [
                'description' => 'Data de resolução da ocorrência. Deve ser uma data anterior ou igual a hoje.',
                'example'     => '2025-09-30',
            ],
        ];
    }
}
