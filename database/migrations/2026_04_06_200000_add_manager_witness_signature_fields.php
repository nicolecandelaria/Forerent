<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            // Move-in: manager witness signature
            $table->string('manager_signature')->nullable()->after('owner_signed_ip');
            $table->timestamp('manager_signed_at')->nullable()->after('manager_signature');
            $table->string('manager_signed_ip')->nullable()->after('manager_signed_at');

            // Move-out: manager witness signature
            $table->string('moveout_manager_signature')->nullable()->after('moveout_owner_signed_ip');
            $table->timestamp('moveout_manager_signed_at')->nullable()->after('moveout_manager_signature');
            $table->string('moveout_manager_signed_ip')->nullable()->after('moveout_manager_signed_at');
        });
    }

    public function down(): void
    {
        Schema::table('leases', function (Blueprint $table) {
            $table->dropColumn([
                'manager_signature',
                'manager_signed_at',
                'manager_signed_ip',
                'moveout_manager_signature',
                'moveout_manager_signed_at',
                'moveout_manager_signed_ip',
            ]);
        });
    }
};
