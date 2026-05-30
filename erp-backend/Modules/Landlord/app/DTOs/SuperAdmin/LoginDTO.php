<?php

namespace Modules\Landlord\DTOs\SuperAdmin;

use Illuminate\Http\Request;

class LoginDTO
{
    public function __construct(
        public readonly string  $method,       // 'password' | 'email_otp' | 'mobile_otp'
        public readonly ?string $email,
        public readonly ?string $mobile,
        public readonly ?string $password,
        public readonly ?string $otp,
        public readonly string  $device_name,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            method:      $request->input('method', 'password'),
            email:       $request->input('email'),
            mobile:      $request->input('mobile'),
            password:    $request->input('password'),
            otp:         $request->input('otp'),
            device_name: $request->input('device_name', $request->userAgent() ?? 'unknown'),
        );
    }
}
