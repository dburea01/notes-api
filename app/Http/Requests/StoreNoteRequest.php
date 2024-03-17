<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
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
            'note' => 'required|max:1000',
            'background_color' => 'hex_color'
        ];
    }

    /**
     * @return array<string>
     */
    public function messages(): array
    {
        return [
            'note.required' => __('the note is mandatory'),
            'note.max' => __('the note is too long', ['characters' => 1000]),
            'background_color.hex_color' => __('the background color is not an hex value'),
        ];
    }
}
