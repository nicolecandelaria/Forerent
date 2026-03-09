<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            // Change from VARCHAR(255) to TEXT so we can store JSON array of up to 3 image paths
            $table->text('image_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            $table->string('image_path')->nullable()->change();
        });
    }
};
