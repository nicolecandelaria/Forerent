<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class BedSeeder extends Seeder
{
    public function run(): void
    {
        Unit::all()->each(function ($unit) {
            $bedCount = $unit->unit_cap ?? 4;

            for ($i = 1; $i <= $bedCount; $i++) {

                $bunkNumber = ceil($i / 2);
                $position = ($i % 2 != 0) ? 'Lower Deck' : 'Upper Deck';

                Bed::create([
                    'unit_id' => $unit->unit_id,
                    'bed_number' => "Bed {$bunkNumber} - {$position}",

                    'status' => 'Vacant',
                ]);
            }
        });
    }
}
