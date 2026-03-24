<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    protected Generator $faker;

    public function run(): void
    {
        $this->faker = app(Generator::class);

        // Landlords (keep as is)
        $landlords = User::where('role', 'landlord')->get();
        foreach ($landlords as $landlord) {
            Announcement::factory(8)
                ->authorRole('landlord')
                ->create(['author_id' => $landlord->user_id]);
        }

        // Managers
        $managers = User::where('role', 'manager')->get();
        $totalAnnouncements = 15;
        $managerCount = $managers->count();

        if ($managerCount > 0) {
            // Divide announcements per manager (evenly)
            $perManager = intdiv($totalAnnouncements, $managerCount);
            $remainder = $totalAnnouncements % $managerCount;

            foreach ($managers as $index => $manager) {
                $count = $perManager;

                // Distribute remainder among the first few managers
                if ($index < $remainder) {
                    $count++;
                }

                Announcement::factory($count)
                    ->authorRole('manager')
                    ->create(['author_id' => $manager->user_id]);
            }
        }
    }
}
