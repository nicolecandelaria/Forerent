<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Generator;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    protected Generator $faker;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->faker = app(Generator::class);


        $this->call([
            UserSeeder::class,
            PropertySeeder::class,
            UnitSeeder::class,
            LeaseSeeder::class,
            BillingSeeder::class,
            UtilityBillSeeder::class,
            TransactionSeeder::class,
            MaintenanceSeeder::class,
            TransactionSeeder::class,
            AnnouncementSeeder::class,
        ]);
    }
}
