<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties', 'property_id')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->enum('category', [
                'property_photo',
                'business_permit',
                'bir_2303',
                'inspection_report',
                'barangay_clearance',
                'occupancy_permit',
            ]);
            $table->enum('visibility', ['owner_manager', 'all']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_documents');
    }
};
