<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'checkin' => 'sometimes|date',
            'checkout' => 'sometimes|date|after:checkin',
            'hospedes' => 'sometimes|integer|min:1',
            'estado' => 'sometimes|in:pendente,confirmada,cancelada',
            'observacoes' => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'checkin.date' => 'A data de check-in deve ser válida.',
            'checkout.date' => 'A data de checkout deve ser válida.',
            'checkout.after' => 'A data de checkout deve ser posterior ao check-in.',
            'estado.in' => 'O estado deve ser pendente, confirmada ou cancelada.',
        ];
    }
}


