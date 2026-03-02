<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'nombre' => ['nullable', 'string', 'max:100'],
            'apellido' => ['nullable', 'string', 'max:100'],
            'correo' => ['required', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'regex:/^[\d\s\+\-\(\)]{5,20}$/', 'max:20'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'Ingrese un correo electrónico válido.',
            'correo.max' => 'El correo electrónico no puede exceder 255 caracteres.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
            'apellido.max' => 'El apellido no puede exceder 100 caracteres.',
            'telefono.regex' => 'El teléfono solo puede contener números, espacios, +, -, ( y ).',
            'telefono.max' => 'El teléfono no puede exceder 20 caracteres.',
            'comentario.max' => 'El comentario no puede exceder 1000 caracteres.',
        ];
    }
}
