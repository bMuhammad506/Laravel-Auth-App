<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Http\Requests\SendPasswordResetOtpRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\ResetPasswordOtpMail;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());

        Mail::to($user->email)->queue(new WelcomeMail($user));

        return response()->json([
            'data' => new UserResource($user),
            'message' => 'User successfully registered',
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());

        return response()->json([
            'data' => [
                'user' => $data['user'],
                'access_token' => $data['token'],
            ],
            'message' => 'Successfully logged in',
        ]);
    }

    public function sendPasswordResetOtp(SendPasswordResetOtpRequest $request)
    {
        $otp = $this->authService->sendPasswordResetOtp($request->email);

        Mail::to($request->email)->queue(new ResetPasswordOtpMail($otp));

        return response()->json([
            'message' => 'OTP has been sent to your email.',
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        if (!$this->authService->validatePasswordResetOtp($request->email, $request->otp)) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        $this->authService->resetPassword($request->email, $request->password);

        return response()->json(['message' => 'Password successfully reset.']);
    }
}
