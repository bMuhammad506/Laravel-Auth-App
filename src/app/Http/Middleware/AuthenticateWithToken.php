<?php

namespace App\Http\Middleware;

use App\Services\TokenServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithToken
{
    protected $tokenService;

    public function __construct(TokenServiceInterface $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized. No token provided.'], 401);
        }

        $token = substr($authHeader, 7);

        $user = $this->tokenService->validateToken($token);

        if (!$user) {
            return response()->json(['message' => 'Unauthorized. Invalid or expired token. Try logging in again.'], 401);
        }

        $request->setUserResolver(fn() => $user);

        return $next($request);
    }
}
