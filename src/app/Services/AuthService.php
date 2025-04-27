<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\UserResource;
use App\Services\TokenServiceInterface;
use App\Models\PasswordReset;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AuthService
{
    protected $tokenService;

    public function __construct(TokenServiceInterface $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }

    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
        }

        return [
            'user' => new UserResource($user),
            'token' => $this->tokenService->createToken($user->id, 15),
        ];
    }

    public function sendPasswordResetOtp(string $email): string
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages(['email' => ['No user found with this email address.']]);
        }

        $otp = Str::random(6);
        $hashedOtp = Hash::make($otp);
        $expiresAt = Carbon::now()->addMinutes(15);

        PasswordReset::create([
            'email' => $email,
            'otp_code' => $hashedOtp,
            'expires_at' => $expiresAt,
        ]);

        return $otp;
    }

    public function validatePasswordResetOtp(string $email, string $otp): bool
    {
        $passwordReset = PasswordReset::where('email', $email)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$passwordReset) {
            return false;
        }

        return Hash::check($otp, $passwordReset->otp_code);
    }

    public function resetPassword(string $email, string $newPassword): User
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages(['email' => ['No user found with this email address.']]);
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        PasswordReset::where('email', $email)->delete();

        return $user;
    }
}
