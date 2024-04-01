<?php

namespace App\Http\Requests;

use App\Models\Organization;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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

        /** @var Organization $organization */
        $organization = $this->route('organization');

        return [
            /**
             * The email of the user you want to register. This email must be unique for an organization.
             */
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(fn (Builder $query) => $query->where('organization_id', $organization->id)),
            ],
            /**
             * The password of the user you want to register. The password must have 8 characters minimum.
             */
            'password' => ['required', 'confirmed', Password::min(8)],
            'last_name' => 'required|max:50',
            'first_name' => 'max:50',
        ];
    }

    /**
     * @return array<string>
     */
    public function messages(): array
    {
        return [
            'email.required' => __('the email is mandatory to register'),
            'email.email' => __('the email is not valid'),
            'email.unique' => __('the email already exists for this organization'),

            'password.required' => __('the password is mandatory to register'),
            'password.confirmed' => __('the passwords are not identical'),
            'password.min' => __('the password must have at least 8 characters'),

            'last_name.required' => __('the last name is required to register'),
            'last_name.max' => __('the last name must have 50 characters max'),

            'first_name.max' => __('the first name must have 50 characters max'),

        ];
    }
}
