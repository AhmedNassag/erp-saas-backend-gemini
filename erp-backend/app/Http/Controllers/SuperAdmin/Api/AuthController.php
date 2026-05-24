<?php

namespace App\Http\Controllers\SuperAdmin\Api;

use App\DTOs\SuperAdmin\LoginDTO;
use App\Http\Controllers\Controller;
use App\Services\SuperAdmin\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    // =========================================================================
    // Method 1: Email + Password
    // POST /api/admin/auth/login
    // =========================================================================

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $result = $this->authService->loginWithPassword(LoginDTO::fromRequest($request));
            return $this->successResponse($result);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'بيانات الدخول غير صحيحة.',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    // =========================================================================
    // Method 2: Email OTP — Step 1: Request OTP
    // POST /api/admin/auth/email-otp/send
    // =========================================================================

    public function sendEmailOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $this->authService->sendEmailOtp($request->input('email'));
            return response()->json([
                'status'  => 'success',
                'message' => 'تم إرسال كود التحقق إلى بريدك الإلكتروني.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'البريد الإلكتروني غير مسجل.',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    // =========================================================================
    // Method 2: Email OTP — Step 2: Verify OTP
    // POST /api/admin/auth/email-otp/verify
    // =========================================================================

    public function verifyEmailOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        try {
            $result = $this->authService->verifyEmailOtp(LoginDTO::fromRequest($request));
            return $this->successResponse($result);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'الكود غير صحيح أو منتهي الصلاحية.',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    // =========================================================================
    // Method 3: Mobile OTP — Step 1: Request OTP
    // POST /api/admin/auth/mobile-otp/send
    // =========================================================================

    public function sendMobileOtp(Request $request): JsonResponse
    {
        $request->validate([
            'mobile' => 'required|string',
        ]);

        try {
            $this->authService->sendMobileOtp($request->input('mobile'));
            return response()->json([
                'status'  => 'success',
                'message' => 'تم إرسال كود التحقق إلى رقم موبايلك.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'رقم الموبايل غير مسجل.',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    // =========================================================================
    // Method 3: Mobile OTP — Step 2: Verify OTP
    // POST /api/admin/auth/mobile-otp/verify
    // =========================================================================

    public function verifyMobileOtp(Request $request): JsonResponse
    {
        $request->validate([
            'mobile' => 'required|string',
            'otp'    => 'required|string|size:6',
        ]);

        try {
            $result = $this->authService->verifyMobileOtp(LoginDTO::fromRequest($request));
            return $this->successResponse($result);
        } catch (ValidationException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'الكود غير صحيح أو منتهي الصلاحية.',
                'errors'  => $e->errors(),
            ], 422);
        }
    }

    // =========================================================================
    // Logout
    // POST /api/admin/auth/logout
    // =========================================================================

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user('super_admin'));

        return response()->json([
            'status'  => 'success',
            'message' => 'تم تسجيل الخروج بنجاح.',
        ]);
    }

    // POST /api/admin/auth/logout-all
    public function logoutAll(Request $request): JsonResponse
    {
        $this->authService->logoutAll($request->user('super_admin'));

        return response()->json([
            'status'  => 'success',
            'message' => 'تم تسجيل الخروج من جميع الأجهزة.',
        ]);
    }

    // =========================================================================
    // Me — Get current admin profile
    // GET /api/admin/auth/me
    // =========================================================================

    public function me(Request $request): JsonResponse
    {
        $admin = $request->user('super_admin');

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'     => $admin->id,
                'name'   => $admin->name,
                'email'  => $admin->email,
                'mobile' => $admin->mobile,
            ],
        ]);
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    private function successResponse(array $result): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'تم تسجيل الدخول بنجاح.',
            'token'   => $result['token'],
            'admin'   => [
                'id'     => $result['admin']->id,
                'name'   => $result['admin']->name,
                'email'  => $result['admin']->email,
                'mobile' => $result['admin']->mobile,
            ],
        ]);
    }
}
