<?php

namespace App\Services\SuperAdmin;

use App\DTOs\SuperAdmin\LoginDTO;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthService
{
    // OTP expires after 10 minutes
    private const OTP_TTL_MINUTES = 10;

    // =========================================================================
    // Method 1: Email + Password
    // =========================================================================

    /**
     * Authenticate with email and password, return token.
     *
     * @throws ValidationException
     */
    public function loginWithPassword(LoginDTO $dto): array
    {
        $admin = SuperAdmin::where('email', $dto->email)->first();

        if (! $admin || ! Hash::check($dto->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        $this->ensureActive($admin);

        return $this->issueToken($admin, $dto->device_name);
    }

    // =========================================================================
    // Method 2: Email + OTP
    // =========================================================================

    /**
     * Step 1 — Generate and send OTP to email.
     *
     * @throws ValidationException
     */
    public function sendEmailOtp(string $email): void
    {
        $admin = SuperAdmin::where('email', $email)->first();

        if (! $admin) {
            throw ValidationException::withMessages([
                'email' => ['البريد الإلكتروني غير مسجل.'],
            ]);
        }

        $this->ensureActive($admin);
        $this->generateAndSendOtp($admin, 'email');
    }

    /**
     * Step 2 — Verify email OTP and return token.
     *
     * @throws ValidationException
     */
    public function verifyEmailOtp(LoginDTO $dto): array
    {
        $admin = SuperAdmin::where('email', $dto->email)->first();

        if (! $admin || ! $admin->isOtpValid($dto->otp)) {
            throw ValidationException::withMessages([
                'otp' => ['الكود غير صحيح أو منتهي الصلاحية.'],
            ]);
        }

        $admin->clearOtp();

        return $this->issueToken($admin, $dto->device_name);
    }

    // =========================================================================
    // Method 3: Mobile + OTP
    // =========================================================================

    /**
     * Step 1 — Generate and send OTP to mobile (SMS).
     *
     * @throws ValidationException
     */
    public function sendMobileOtp(string $mobile): void
    {
        $admin = SuperAdmin::where('mobile', $mobile)->first();

        if (! $admin) {
            throw ValidationException::withMessages([
                'mobile' => ['رقم الموبايل غير مسجل.'],
            ]);
        }

        $this->ensureActive($admin);
        $this->generateAndSendOtp($admin, 'mobile');
    }

    /**
     * Step 2 — Verify mobile OTP and return token.
     *
     * @throws ValidationException
     */
    public function verifyMobileOtp(LoginDTO $dto): array
    {
        $admin = SuperAdmin::where('mobile', $dto->mobile)->first();

        if (! $admin || ! $admin->isOtpValid($dto->otp)) {
            throw ValidationException::withMessages([
                'otp' => ['الكود غير صحيح أو منتهي الصلاحية.'],
            ]);
        }

        $admin->clearOtp();

        return $this->issueToken($admin, $dto->device_name);
    }

    // =========================================================================
    // Logout
    // =========================================================================

    public function logout(SuperAdmin $admin): void
    {
        $admin->currentAccessToken()->delete();
    }

    public function logoutAll(SuperAdmin $admin): void
    {
        $admin->tokens()->delete();
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    private function ensureActive(SuperAdmin $admin): void
    {
        if (! $admin->is_active) {
            throw ValidationException::withMessages([
                'email' => ['هذا الحساب موقوف. تواصل مع الدعم الفني.'],
            ]);
        }
    }

    private function generateAndSendOtp(SuperAdmin $admin, string $channel): void
    {
        $otp = (string) random_int(100000, 999999);

        $admin->update([
            'otp_code'       => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(self::OTP_TTL_MINUTES),
        ]);

        if ($channel === 'email') {
            // TODO: Replace with a proper Mailable class
            Mail::raw("كود التحقق الخاص بك: {$otp} (صالح لمدة " . self::OTP_TTL_MINUTES . " دقائق)", function ($message) use ($admin) {
                $message->to($admin->email)->subject('كود تسجيل الدخول - ERP SaaS');
            });
        }

        if ($channel === 'mobile') {
            // TODO: Integrate SMS provider (e.g. Vonage, Twilio, local gateway)
            // SmsService::send($admin->mobile, "كود التحقق: {$otp}");
            \Illuminate\Support\Facades\Log::info("SMS OTP for {$admin->mobile}: {$otp}");
        }
    }

    private function issueToken(SuperAdmin $admin, string $deviceName): array
    {
        // Revoke existing tokens for same device
        $admin->tokens()->where('name', $deviceName)->delete();

        $token = $admin->createToken($deviceName, ['super_admin'])->plainTextToken;

        return [
            'token' => $token,
            'admin' => $admin,
        ];
    }
}
