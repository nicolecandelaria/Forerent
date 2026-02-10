<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Announcement::factory(15)->authorRole('landlord')->create(['author_id' => 3]);
        Announcement::factory(15)->authorRole('manager')->create(['author_id' => 2]);
    }
}
