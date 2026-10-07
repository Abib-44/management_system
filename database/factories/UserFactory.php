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
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake('it_IT')->firstName().' '.fake('it_IT')->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone' => fake('it_IT')->phoneNumber(),
            'active' => fake()->boolean(90),
            'password' => static::$password ??= Hash::make('pas'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Assign a Spatie role after creation. Users with a role are always active.
     */
    public function withRole(string $role): static
    {
        return $this
            ->state(fn (array $attributes) => [
                'active' => true,
            ])
            ->afterCreating(function (User $user) use ($role): void {
                $user->assignRole($role);
            });
    }

    public function admin(): static
    {
        return $this->withRole('admin');
    }

    public function secretary(): static
    {
        return $this->withRole('secretary');
    }

    public function treasurer(): static
    {
        return $this->withRole('treasurer');
    }

    public function teacher(): static
    {
        return $this->withRole('teacher');
    }

    public function services(): static
    {
        return $this->withRole('services');
    }

    public function superAdmin(): static
    {
        return $this->withRole('super_admin');
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}
