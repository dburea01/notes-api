<?php

namespace App\Http\Requests;

use App\Models\Note;
use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreNoteRequest extends FormRequest
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
            'note' => 'required|max:1000',
            'background_color' => 'hex_color',
        ];
    }

    public function after(): array
    {

        return [
            function (Validator $validator) {

                /** @var Organization $organization */
                $organization = $this->route('organization');

                $maxNotesPerOrganization = config('params.max_notes_per_organization');
                $quantityNotes = Note::where('organization_id', $organization->id)->count();

                if ($quantityNotes >= $maxNotesPerOrganization) {
                    $validator->errors()->add(
                        'max_notes_per_organization',
                        __('you have reached the maximum of notes per organization', ['quantityNotesPerOrganization' => $quantityNotes]),
                    );
                }
            },
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
