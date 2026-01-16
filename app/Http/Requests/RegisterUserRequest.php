<?php

namespace App\Http\Requests;

use App\Rules\Users\UniqueNicknameRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'nickname' => ['required', 'string', 'min:3', 'max:32', 'alpha_dash:ascii', new UniqueNicknameRule()],
            'avatar'   => ['required', 'file', 'max:5120', 'mimes:jpg,webp,png'],
        ];
    }
}
