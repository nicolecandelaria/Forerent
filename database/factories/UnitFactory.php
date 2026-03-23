<?php

namespace Database\Factories;

use App\Models\Bed;
use App\Models\Property;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    protected $model = Unit::class;

    public function definition(): array
    {
        $bedType = $this->faker->randomElement(['Single', 'Bunk']);

        // Determine room capacity based on bed_type
        $roomCap = match ($bedType) {
            'Single' => 1,
            'Bunk'   => 4,
            default  => 1,
        };

        return [
            'property_id'   => Property::factory(),
            'manager_id'    => User::where('role', 'manager')->inRandomOrder()->value('user_id'),
            'floor_number'  => 1,
            'unit_number'   => '0000', // will be overridden by seeder if needed
            'occupants'     => $this->faker->randomElement(['Male', 'Female', 'Co-ed']),
            'living_area'   => $this->faker->randomFloat(1, 80, 200),
            'furnishing'    => $this->faker->randomElement(['Bare', 'Semi-furnished', 'Fully Furnished']),
            'bed_type'      => $bedType,
            'room_cap'      => $roomCap,
            'price'         => $this->faker->randomFloat(2, 3000, 15000),
            'amenities'     => null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Unit $unit) {

            // Determine number of beds based on bed_type
            $bedCount = match ($unit->bed_type) {
                'Single' => 1,
                'Bunk'   => 4,
                default  => 1,
            };

            // Create beds for this unit
            for ($i = 1; $i <= $bedCount; $i++) {
                Bed::factory()->create([
                    'unit_id'    => $unit->unit_id,
                    'bed_number' => 'B' . $i,
                ]);
            }
        });
    }
}
