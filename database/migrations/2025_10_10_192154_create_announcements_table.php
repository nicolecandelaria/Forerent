<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id('announcement_id')->primary();
            $table->foreignId('author_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('property_id')->nullable()->constrained('properties', 'property_id')->nullOnDelete();
            $table->string('headline');
            $table->text('details');
            $table->enum('sender_role', ['landlord', 'manager'])->default('landlord');
            $table->enum('recipient_role', ['manager', 'tenant'])->default('manager');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
