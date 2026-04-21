<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'role_id' => 2, // Kita set default ke Customer (ID 2)
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'password' => static::$password ??= Hash::make('password'),
            'is_verified' => true, // User dari factory otomatis dianggap sudah verifikasi
            'otp' => null,
        ];
    }

    /**
     * Fungsi unverified kita hapus karena kolom email_verified_at sudah tidak ada
     */
}