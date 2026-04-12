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
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(
                ['category', 'transaction_type', 'transaction_date'],
                'transactions_forecast_lookup_idx'
            );

            $table->index(
                ['transaction_date', 'transaction_id'],
                'transactions_forecast_sort_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_forecast_lookup_idx');
            $table->dropIndex('transactions_forecast_sort_idx');
        });
    }
};
