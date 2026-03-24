<?php

namespace Database\Factories;

use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        $faker = $this->faker ?? app(Generator::class);

        $firstName = $faker->firstName();
        $lastName  = $faker->lastName();

        $email = strtolower($firstName . '.' . $lastName) . '@example.com';

        return [
            'first_name' => $firstName,
            'last_name'  => $lastName,

            'email' => $email,

            'role' => $faker->randomElement(['tenant', 'manager', 'landlord']),

            'contact' => $faker->phoneNumber(),

            'profile_img' => 'https://i.pravatar.cc/150?u=' . $email,

            'password' => Hash::make('password'),

            'email_verified_at' => now(),
            'phone_verified_at' => null,

            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}