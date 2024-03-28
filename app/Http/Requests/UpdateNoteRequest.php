<?php

namespace App\Http\Requests;

use App\Models\Note;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->user() && $this->method() == 'POST') {
            return $this->user()->can('create', [Note::class, $this->route('organization')]);
        }

        if ($this->user() && $this->method() == 'PUT') {
            return $this->user()->can('update', [$this->route('note'), $this->route('organization')]);
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'note' => 'max:1000',
            'background_color' => 'hex_color',
        ];
    }
}
