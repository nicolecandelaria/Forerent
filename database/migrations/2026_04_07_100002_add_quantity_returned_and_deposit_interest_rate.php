<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add quantity_returned to move_out_inspections for partial return tracking
        Schema::table('move_out_inspections', function (Blueprint $table) {
            $table->unsignedInteger('quantity_returned')->nullable()->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('move_out_inspections', function (Blueprint $table) {
            $table->dropColumn('quantity_returned');
        });
    }
};
