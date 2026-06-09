<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    protected $fillable = [
        'email',
        'otp',
        'expires_at',
        'attempts',
    ];

    public function isExpired()
    {
        return $this->expires_at < now();
    }
}
