<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->string('tenant_signature')->nullable()->after('early_termination_fee');
            $table->timestamp('tenant_signed_at')->nullable()->after('tenant_signature');
            $table->string('tenant_signed_ip')->nullable()->after('tenant_signed_at');

            $table->string('owner_signature')->nullable()->after('tenant_signed_ip');
            $table->timestamp('owner_signed_at')->nullable()->after('owner_signature');
            $table->string('owner_signed_ip')->nullable()->after('owner_signed_at');

            $table->string('signed_contract_path')->nullable()->after('owner_signed_ip');
            $table->boolean('contract_agreed')->default(false)->after('signed_contract_path');
        });
    }

    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropColumn([
                'tenant_signature',
                'tenant_signed_at',
                'tenant_signed_ip',
                'owner_signature',
                'owner_signed_at',
                'owner_signed_ip',
                'signed_contract_path',
                'contract_agreed',
            ]);
        });
    }
};
