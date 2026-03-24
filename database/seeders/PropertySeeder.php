<?php

namespace Database\Seeders;

use App\Models\Property;
use Faker\Generator;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    protected Generator $faker;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->faker = app(Generator::class);

        // Create 3 properties owned by user with ID 3
        Property::factory()
            ->count(3)
            ->create(['owner_id' => 3]);
    }
}
