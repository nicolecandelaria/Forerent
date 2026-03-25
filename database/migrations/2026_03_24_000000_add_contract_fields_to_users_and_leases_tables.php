<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('gender', ['Male', 'Female'])->nullable()->after('last_name');
            $table->text('permanent_address')->nullable()->after('contact');
            $table->string('government_id_type', 100)->nullable()->after('permanent_address');
            $table->string('government_id_number', 100)->nullable()->after('government_id_type');
            $table->string('company_school', 255)->nullable()->after('government_id_number');
            $table->string('position_course', 255)->nullable()->after('company_school');
            $table->string('emergency_contact_name', 255)->nullable()->after('position_course');
            $table->string('emergency_contact_relationship', 100)->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_number', 50)->nullable()->after('emergency_contact_relationship');
        });

        Schema::table('leases', function (Blueprint $table) {
            $table->unsignedTinyInteger('monthly_due_date')->nullable()->after('security_deposit');
            $table->decimal('late_payment_penalty', 8, 2)->nullable()->after('monthly_due_date');
            $table->decimal('short_term_premium', 8, 2)->nullable()->after('late_payment_penalty');
            $table->decimal('reservation_fee_paid', 8, 2)->nullable()->after('short_term_premium');
            $table->decimal('early_termination_fee', 8, 2)->nullable()->after('reservation_fee_paid');

            // Move-out fields
            $table->text('forwarding_address')->nullable()->after('move_out');
            $table->string('reason_for_vacating', 255)->nullable()->after('forwarding_address');
            $table->string('deposit_refund_method', 100)->nullable()->after('reason_for_vacating');
            $table->string('deposit_refund_account', 255)->nullable()->after('deposit_refund_method');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'permanent_address',
                'government_id_type',
                'government_id_number',
                'company_school',
                'position_course',
                'emergency_contact_name',
                'emergency_contact_relationship',
                'emergency_contact_number',
            ]);
        });

        Schema::table('leases', function (Blueprint $table) {
            $table->dropColumn([
                'monthly_due_date',
                'late_payment_penalty',
                'short_term_premium',
                'reservation_fee_paid',
                'early_termination_fee',
                'forwarding_address',
                'reason_for_vacating',
                'deposit_refund_method',
                'deposit_refund_account',
            ]);
        });
    }
};
