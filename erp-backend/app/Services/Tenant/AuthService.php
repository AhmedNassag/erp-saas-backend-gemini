<?php

namespace App\Services\Tenant;

use App\DTOs\Tenant\AuthDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Authenticate a tenant user and return token + user data.
     *
     * @throws ValidationException
     */
    public function authenticate(AuthDTO $dto): array
    {
        $user = User::where('email', $dto->email)->first();

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        // Revoke old tokens for this device to avoid token bloat
        $user->tokens()->where('name', $dto->device_name)->delete();

        $token = $user->createToken($dto->device_name)->plainTextToken;

        return [
            'token'       => $token,
            'user'        => $user,
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ];
    }
}
