<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_category_id')->nullable()->after('tenant_id');
            $table->foreign('payment_category_id')->references('payment_category_id')->on('payment_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payment_requests', function (Blueprint $table) {
            $table->dropForeign(['payment_category_id']);
            $table->dropColumn('payment_category_id');
        });
    }
};
