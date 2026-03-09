<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('tenant_id');
            $table->tinyInteger('rating');               // 1–5 stars
            $table->string('experience_tag')->nullable(); // e.g. "Fully Resolved"
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('request_id')
                  ->references('request_id')
                  ->on('maintenance_requests')
                  ->onDelete('cascade');

            $table->foreign('tenant_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');

            // One feedback per tenant per ticket
            $table->unique(['request_id', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_feedback');
    }
};
