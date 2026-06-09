<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->numerify('09########'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => null,
            'approved' => true,
            'profile_image' => null,
            'location' => fake()->address(),
            $this->afterCreating(function (User $user) {
                $user->assignRole(...$roles = ['admin', 'teacher', 'student']);
            }),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => 'admin@school.com',
        ]);
    }

    public function teacher(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => fake()->unique()->safeEmail(),
        ]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => fake()->unique()->safeEmail(),
        ]);
    }

    public function parent(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => fake()->unique()->safeEmail(),
        ]);
    }
}
