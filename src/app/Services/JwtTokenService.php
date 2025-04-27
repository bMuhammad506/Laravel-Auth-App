<?php

namespace App\Services;

use App\Models\User;
use App\Services\TokenServiceInterface;

class JwtTokenService implements TokenServiceInterface
{
    protected $secret;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET', 'aM2eC8yB5uR3nV5oP4lK');
    }

    /**
     * Generate a new JWT token.
     *
     * @param int $userId
     * @return string
     */
    public function createToken(int $userId, int $expiryMinutes = 15): string
    {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];

        $payload = [
            'sub' => $userId,
            'exp' => time() + ($expiryMinutes * 60)
        ];

        $base64Header = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
        $base64Payload = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');

        $signature = hash_hmac('sha256', "$base64Header.$base64Payload", $this->secret, true);
        $base64Signature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        return "$base64Header.$base64Payload.$base64Signature";
    }

    /**
     * Validate a JWT token.
     *
     * @param string $token
     * @return mixed (User model or false)
     */
    public function validateToken(string $token)
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        list($base64Header, $base64Payload, $base64Signature) = $parts;

        $expectedSignature = rtrim(strtr(base64_encode(
            hash_hmac('sha256', "$base64Header.$base64Payload", $this->secret, true)
        ), '+/', '-_'), '=');

        if (!hash_equals($base64Signature, $expectedSignature)) {
            return false;
        }

        $payload = json_decode(base64_decode(strtr($base64Payload, '-_', '+/')), true);

        if (!$payload || time() > $payload['exp']) {
            return false;
        }

        return User::find($payload['sub']) ?: false;
    }
}
