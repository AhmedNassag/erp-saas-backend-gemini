<?php

namespace App\DTOs\Tenant;

use Illuminate\Http\Request;

class AuthDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $device_name = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            email: $request->input('email'),
            password: $request->input('password'),
            device_name: $request->input('device_name', $request->userAgent() ?? 'unknown'),
        );
    }
}
