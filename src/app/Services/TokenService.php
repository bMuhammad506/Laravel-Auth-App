<?php

namespace App\Services;

use App\Models\Token;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\TokenServiceInterface;

class TokenService implements TokenServiceInterface
{
    /**
     * Generate a new access token and store it.
     *
     * @param int $userId
     * @return string
     */
    public function createToken(int $userId, int $expiryMinutes = 15): string
    {
        $token = Str::random(64);

        Token::create([
            'user_id'    => $userId,
            'token'      => hash('sha256', $token),
            'expires_at' => Carbon::now()->addMinutes($expiryMinutes),
        ]);

        return $token;
    }

    /**
     * Validate a token.
     *
     * @param string $token
     * @return mixed (User model or false)
     */
    public function validateToken(string $token)
    {
        $hashedToken = hash('sha256', $token);

        $record = Token::where('token', $hashedToken)
                    ->where('expires_at', '>', now())
                    ->first();

        return $record ? $record->user : false;
    }
}
