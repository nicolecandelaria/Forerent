<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    protected Generator $faker;

    public function run(): void
    {
        $this->faker = app(Generator::class);

        User::factory()->create([
            'first_name' => 'Tricia',
            'last_name' => 'Tenant',
            'email' => 'tenant@example.com',
            'role' => 'tenant',
            'password' => Hash::make('password'),
            'government_id_type' => 'National ID',
            'government_id_number' => 'PSN-20241234567',
            'government_id_image' => 'government-ids/sample-id-tricia.jpg',
        ]);

        User::factory()->create([
            'first_name' => 'Tanya',
            'last_name' => 'Torres',
            'email' => 'tenant2@example.com',
            'role' => 'tenant',
            'password' => Hash::make('password'),
            'government_id_type' => 'Passport',
            'government_id_number' => 'P-987654321',
            'government_id_image' => 'government-ids/sample-id-tanya.jpg',
        ]);

        User::factory()->create([
            'first_name' => 'Marcus',
            'last_name' => 'Manager',
            'email' => 'manager@example.com',
            'role' => 'manager',
            'password' => Hash::make('password'),
            'government_id_type' => "Driver's License",
            'government_id_number' => 'N01-12-345678',
            'government_id_image' => 'government-ids/sample-id-marcus.jpg',
        ]);

        User::factory()->create([
            'first_name' => 'Mia',
            'last_name' => 'Martinez',
            'email' => 'manager2@example.com',
            'role' => 'manager',
            'password' => Hash::make('password'),
            'government_id_type' => 'UMID',
            'government_id_number' => 'CRN-0012-3456789',
            'government_id_image' => 'government-ids/sample-id-mia.jpg',
        ]);

        User::factory()->create([
            'first_name' => 'Liam',
            'last_name' => 'Landlord',
            'email' => 'landlord@example.com',
            'role' => 'landlord',
            'password' => Hash::make('password'),
            'company_school' => 'ABC COMPANY',
            'government_id_type' => 'Passport',
            'government_id_number' => 'EB-1234567',
            'government_id_image' => 'government-ids/sample-id-liam.jpg',
        ]);


        User::factory()
            ->count(148)
            ->create([
                'role' => 'tenant',
                'password' => Hash::make('password'),
            ]);

        User::factory()
            ->count(3)
            ->create([
                'role' => 'manager',
                'password' => Hash::make('password'),
            ]);
    }
}
