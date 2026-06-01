<?php

namespace App\Http\Controllers\Tenant\Api;

use App\Http\Controllers\Controller;
use App\DTOs\Tenant\AuthDTO;
use App\Services\Tenant\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    // حقن الخدمة (Service Injection)
    public function __construct(protected AuthService $authService)
    {
        
    }

    /**
     * API Login
     */
    public function login(Request $request): JsonResponse
    {
        // 1. الـ Validation الافتراضي للطلب
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // 2. تحويل الطلب لـ DTO ونقله للـ Service Layer
        $authData = $this->authService->authenticate(
            AuthDTO::fromRequest($request)
        );

        // 3. الرد بالـ JSON النظيف للـ Vue 3
        return response()->json([
            'status'      => 'success',
            'message'     => 'تم تسجيل الدخول بنجاح',
            'token'       => $authData['token'],
            'user'        => [
                'id'    => $authData['user']->id,
                'name'  => $authData['user']->name,
                'email' => $authData['user']->email,
                'roles' => $authData['user']->roles,
            ],
            'permissions' => $authData['permissions'],
        ], 200);
    }

    /**
     * API Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'تم تسجيل الخروج بنجاح'
        ], 200);
    }
}