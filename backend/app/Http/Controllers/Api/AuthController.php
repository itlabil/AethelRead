<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class AuthController extends ApiController
{
    /**
     * Authenticate admin and return JWT token.
     * Used by Android client for admin access (if needed).
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        try {
            $token = JWTAuth::attempt($credentials);

            if (! $token) {
                return $this->errorResponse('Invalid credentials', 401);
            }

            // Check if user is active
            $user = JWTAuth::user();
            if (! $user->isActive()) {
                // Only invalidate if blacklist is enabled
                if (config('jwt.blacklist_enabled')) {
                    JWTAuth::invalidate($token);
                }
                return $this->errorResponse('Your account has been deactivated.', 401);
            }

        } catch (JWTException $e) {
            return $this->errorResponse('Could not create token', 500);
        }

        return $this->successResponse(
            $this->buildTokenResponse($token),
            'Login successful'
        );
    }

    /**
     * Logout and invalidate the current token.
     */
    public function logout(): JsonResponse
    {
        try {
            if (config('jwt.blacklist_enabled')) {
                JWTAuth::invalidate(JWTAuth::getToken());
            }
        } catch (JWTException $e) {
            return $this->errorResponse('Failed to invalidate token', 500);
        }

        return $this->successResponse(null, 'Logout successful');
    }

    /**
     * Refresh the current JWT token.
     */
    public function refresh(): JsonResponse
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
        } catch (JWTException $e) {
            return $this->errorResponse('Could not refresh token', 401);
        }

        return $this->successResponse(
            $this->buildTokenResponse($newToken),
            'Token refreshed'
        );
    }

    /**
     * Get the authenticated user.
     */
    public function me(): JsonResponse
    {
        return $this->successResponse(
            auth('api')->user(),
            'User retrieved'
        );
    }

    /**
     * Build consistent token response structure.
     */
    private function buildTokenResponse(string $token): array
    {
        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => config('jwt.ttl') * 60,
        ];
    }
}