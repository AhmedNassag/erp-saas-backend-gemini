<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class SuperAdmin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // Always reads from the landlord (central) database
    protected $connection = 'landlord';

    protected $table = 'super_admins';

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'otp_code',
        'otp_expires_at',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
        'is_active'      => 'boolean',
    ];

    /**
     * Check if the stored OTP is valid (not expired).
     */
    public function isOtpValid(string $code): bool
    {
        return $this->otp_code === $code
            && $this->otp_expires_at
            && $this->otp_expires_at->isFuture();
    }

    /**
     * Clear OTP after successful verification.
     */
    public function clearOtp(): void
    {
        $this->update([
            'otp_code'       => null,
            'otp_expires_at' => null,
        ]);
    }
}
