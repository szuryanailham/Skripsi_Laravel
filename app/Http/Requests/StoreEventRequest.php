<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Pastikan ini true agar bisa digunakan
    }

    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'location'    => 'required|string',
            'date'        => 'required|date',
            'time'        => 'required|date_format:H:i:s',
        ];
    }
}

