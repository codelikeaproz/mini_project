<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'role', 'password', 'avatar',
        'phone_number', 'municipality', 'position', 'is_active',
        'is_verified', 'verification_token', 'email_verified_at',
        'two_factor_code', 'two_factor_expires_at', 'last_login_at',
        'failed_login_attempts', 'locked_until'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password', 'remember_token', 'verification_token', 'two_factor_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'two_factor_expires_at' => 'datetime',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
        ];
    }

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // MDRRMO Role checking methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isMdrrmoStaff()
    {
        return $this->role === 'mdrrmo_staff';
    }

    // Account security methods
    public function isAccountLocked()
    {
        return $this->locked_until && now()->lt($this->locked_until);
    }

    public function incrementFailedLogins()
    {
        $this->increment('failed_login_attempts');

        // Lock account after 5 failed attempts for 15 minutes
        if ($this->failed_login_attempts >= 5) {
            $this->update(['locked_until' => now()->addMinutes(15)]);
        }
    }

    public function resetFailedLogins()
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now()
        ]);
    }

    // 2FA methods
    public function generateTwoFactorCode()
    {
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->update([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(10)
        ]);
    }

    public function isTwoFactorCodeValid($code)
    {
        return $this->two_factor_code === $code &&
               $this->two_factor_expires_at &&
               now()->lt($this->two_factor_expires_at);
    }

    public function clearTwoFactorCode()
    {
        $this->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null
        ]);
    }

    // Email verification
    public function sendEmailVerificationNotification()
    {
        $this->verification_token = Str::random(64);
        $this->save();

        $this->notify(new \App\Notifications\EmailVerificationNotification($this));
    }

    // Relationships for MDRRMO system
    public function reportedIncidents()
    {
        return $this->hasMany(\App\Models\Incident::class, 'reported_by');
    }

    public function assignedIncidents()
    {
        return $this->hasMany(\App\Models\Incident::class, 'assigned_staff');
    }

    public function loginAttempts()
    {
        return $this->hasMany(LoginAttempt::class, 'email', 'email');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
