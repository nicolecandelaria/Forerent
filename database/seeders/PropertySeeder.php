<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Property::factory()
            ->count(3)
            ->create(['owner_id' => 3])
            ->each(function ($property) {
                $floors = rand(1, 5);

                for ($floor = 1; $floor <= $floors; $floor++) {
                    $unitsPerFloor = rand(5, 8);

                    Unit::factory()
                        ->count($unitsPerFloor)
                        ->create([
                            'property_id' => $property->property_id,
                            'floor_number' => $floor,

                            'unit_cap' => collect([4, 6, 8])->random(),
                        ]);
                }
            });
    }
}
