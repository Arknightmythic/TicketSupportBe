<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role === 'customer';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $error = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json([
                'success' =>false,
                'message' => 'Validation errors',
                'data' => $error
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
