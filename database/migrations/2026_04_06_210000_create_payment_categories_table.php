<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_categories', function (Blueprint $table) {
            $table->id('payment_category_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->enum('type', ['income', 'expense'])->default('income');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Seed default categories
        DB::table('payment_categories')->insert([
            ['name' => 'Rent Payment',     'description' => 'Monthly rental payment',                    'type' => 'income',  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Move-In Payment',  'description' => 'Payment upon move-in (advance + deposit)',  'type' => 'income',  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Advance',          'description' => 'Advance rent payment',                      'type' => 'income',  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Deposit',          'description' => 'Security deposit',                          'type' => 'income',  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maintenance',      'description' => 'Maintenance and repair costs',              'type' => 'expense', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vendor Payment',   'description' => 'Payments to vendors and suppliers',         'type' => 'expense', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Move-Out Settlement', 'description' => 'Settlement upon tenant move-out',        'type' => 'expense', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_categories');
    }
};
