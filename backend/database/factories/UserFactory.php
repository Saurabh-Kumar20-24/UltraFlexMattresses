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
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'role'              => 'customer',
            'phone'=>'9'.fake()->numerify('########'),
            'city' => fake()->randomElement([
                'Jaipur',
                'Delhi',
                'Gurugram',
                'Chandigarh',
                'Ludhiana',
                'Jodhpur'
            ]),
            'state' => fake()->randomElement([
                'Rajasthan',
                'Delhi',
                'Haryana',
                'Punjab'
            ]),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin() : static {
       return $this->state(fn ()=>[
          'name' => 'Ultra Flex Admin',
          'email' => 'admin@ultraflexmattresses.com',
          'role' => 'admin'
       ]);
    }

    public function customer(): static {
        return $this->state(fn ()=> [
            'role' => 'customer',
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
