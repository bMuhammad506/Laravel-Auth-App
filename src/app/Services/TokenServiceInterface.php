<?php
namespace App\Services;

interface TokenServiceInterface
{
    public function createToken(int $userId): string;
    public function validateToken(string $token);
}
