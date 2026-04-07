<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserSeeder extends Seeder
{
    protected Generator $faker;

    public function run(): void
    {
        $this->faker = app(Generator::class);

        // Ensure government-ids directory exists and generate placeholder images
        Storage::disk('public')->makeDirectory('government-ids');
        $this->generatePlaceholderIds();

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

    /**
     * Generate placeholder government ID images for seeded users.
     */
    private function generatePlaceholderIds(): void
    {
        $names = [
            'tricia', 'tanya', 'marcus', 'mia', 'liam',
        ];

        foreach ($names as $name) {
            $path = 'government-ids/sample-id-' . $name . '.jpg';

            if (!Storage::disk('public')->exists($path)) {
                // Create a simple placeholder image
                $img = imagecreatetruecolor(400, 250);
                $bg = imagecolorallocate($img, 240, 240, 240);
                $textColor = imagecolorallocate($img, 80, 80, 80);
                imagefilledrectangle($img, 0, 0, 399, 249, $bg);
                imagestring($img, 5, 120, 100, 'Government ID', $textColor);
                imagestring($img, 4, 140, 130, ucfirst($name), $textColor);

                ob_start();
                imagejpeg($img, null, 90);
                $imageData = ob_get_clean();
                imagedestroy($img);

                Storage::disk('public')->put($path, $imageData);
            }
        }
    }
}
