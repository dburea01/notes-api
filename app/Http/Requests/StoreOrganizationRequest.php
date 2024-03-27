<?php

namespace App\Http\Requests;

use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->user() && $this->method() == 'POST') {
            return $this->user()->can('create', Organization::class);
        }

        if ($this->user() && $this->method() == 'PUT') {
            return $this->user()->can('update', $this->route('organization'));
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
            'name' => 'required|max:100',
            'comment' => 'max:100',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ];
    }

    /**
     * @return array<string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('the name of the organization is mandatory'),
            'name.max' => __('the name of the organization is too long', ['characters' => 100]),
            'comment.max' => __('the comment of the organization is too long', ['characters' => 100]),
            'status.required' => __('the status of the organization is required'),
            'status.in' => __('the status is not valid', ['in' => 'ACTIVE,INACTIVE']),
        ];
    }
}
