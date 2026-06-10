<?php

namespace Database\Factories;

use App\Models\Agency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agency_id' => Agency::query()->value('id') ?? Agency::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Un utilisateur `admin` créé via la factory représente le directeur d'agence,
     * qui est `is_owner = true` en production (cf. AgencyRegistrationController,
     * GoogleAuthController, SuperAdminController). Sans ça, l'admin de test serait
     * traité comme un collaborateur sans permission et toute route `agency.can:`
     * lui renverrait un 403.
     *
     * Contournable explicitement : passer `is_owner => false` pour simuler un
     * collaborateur.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (\App\Models\User $user) {
            if ($user->role === 'admin' && $user->is_owner === null) {
                $user->is_owner = true;
            }
        });
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
}
