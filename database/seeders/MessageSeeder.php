<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Message;
use Faker\Generator;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    protected Generator $faker;

    public function run(): void
    {
        $this->faker = app(Generator::class);

        // 1. Find "You" (The currently logged-in Landlord)
        $me = User::where('role', 'landlord')->first();

        // If no landlord exists, create one safely
        if (!$me) {
            $me = User::factory()->create([
                'first_name' => 'Main',
                'last_name' => 'Landlord',
                'email' => 'admin@forent.com',
                'role' => 'landlord',
                'contact' => '09171234567', // Added contact here just in case
            ]);
        }

        // ================================================================
        // 2. CREATE A DUMMY MANAGER (For the "Manager" Tab)
        // ================================================================
        $manager = User::firstOrCreate(
            ['email' => 'manager@forerent.com'],
            [
                'first_name' => 'Jherimiah',
                'last_name' => 'Manager',
                'role' => 'manager',
                'contact' => '09123456789', // <--- FIX: Added required contact field
                'password' => bcrypt('password'),
                'profile_img' => 'https://ui-avatars.com/api/?name=Jherimiah+Manager&background=random'
            ]
        );

        // Seed Conversation with Manager
        Message::create([
            'sender_id' => $manager->user_id,
            'receiver_id' => $me->user_id,
            'message' => 'Good morning! The maintenance for Unit 302 is complete.',
            'created_at' => now()->subHours(4),
            'is_read' => true,
        ]);

        Message::create([
            'sender_id' => $me->user_id,
            'receiver_id' => $manager->user_id,
            'message' => 'Great work, please send me the invoice.',
            'created_at' => now()->subHours(3),
            'is_read' => true,
        ]);

        Message::create([
            'sender_id' => $manager->user_id,
            'receiver_id' => $me->user_id,
            'message' => 'Sent it to your email just now.',
            'created_at' => now()->subMinutes(10),
            'is_read' => false,
        ]);


        // ================================================================
        // 3. CREATE ANOTHER DUMMY OWNER (For the "Owner" Tab)
        // ================================================================
        $partner = User::firstOrCreate(
            ['email' => 'partner@forerent.com'],
            [
                'first_name' => 'Business',
                'last_name' => 'Partner',
                'role' => 'landlord',
                'contact' => '09987654321', // <--- FIX: Added required contact field
                'password' => bcrypt('password'),
                'profile_img' => 'https://ui-avatars.com/api/?name=Business+Partner&background=random'
            ]
        );

        // Seed Conversation with Partner
        Message::create([
            'sender_id' => $partner->user_id,
            'receiver_id' => $me->user_id,
            'message' => 'Hey, did you check the monthly revenue report?',
            'created_at' => now()->subDays(1),
            'is_read' => true,
        ]);

        Message::create([
            'sender_id' => $me->user_id,
            'receiver_id' => $partner->user_id,
            'message' => 'Not yet, I will check it tonight.',
            'created_at' => now()->subHours(5),
            'is_read' => true,
        ]);
    }
}
