<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:'.Config::get('auth.name_min_length'),
                'max:'.Config::get('auth.name_max_length'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
            ],
            'password' => [
                'required',
                'confirmed',
                'min:'.Config::get('auth.password_min_length'),
                'max:'.Config::get('auth.password_max_length'),
                Password::defaults(),
            ],
        ];
    }
}
