<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Untuk fitur Token Login Mobile
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    // Kita tambahkan HasApiTokens agar bisa membuat token login untuk Flutter
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi secara massal
     */
protected $fillable = [
    'username',    // Tambah ini
    'full_name',   // Tambah ini
    'profile_photo', // Tambah ini
    'email',
    'password',
    'phone',
    'address',
    'role_id',
    'otp',
    'is_verified',
];

    /**
     * Data yang disembunyikan saat dikirim ke API (Flutter) demi keamanan
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp', // OTP disembunyikan agar tidak bisa diintip lewat API
    ];

    /**
     * Pengaturan format data otomatis
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean', // Memastikan status verifikasi terbaca true/false
        ];
    }

    /**
     * RELASI: User ini memiliki satu Role (Many to One)
     * Ini menghubungkan user_id ke tabel roles
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}