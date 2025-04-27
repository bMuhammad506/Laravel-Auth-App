<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => 'No user found with this email address.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'otp.required' => 'OTP is required.',
            'otp.string' => 'OTP must be a string.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
