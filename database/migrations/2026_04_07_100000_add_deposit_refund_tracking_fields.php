<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->decimal('deposit_interest_amount', 8, 2)->nullable()->after('deposit_deductions');
            $table->date('deposit_refund_deadline')->nullable()->after('deposit_interest_amount');
            $table->datetime('deposit_refund_completed_at')->nullable()->after('deposit_refund_deadline');
            $table->string('deposit_refund_reference')->nullable()->after('deposit_refund_completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropColumn([
                'deposit_interest_amount',
                'deposit_refund_deadline',
                'deposit_refund_completed_at',
                'deposit_refund_reference',
            ]);
        });
    }
};
