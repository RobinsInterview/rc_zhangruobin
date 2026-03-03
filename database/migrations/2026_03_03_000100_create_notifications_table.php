<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('event_type', 64)->index();
            $table->string('biz_id', 128);
            $table->string('idempotency_key', 255)->unique();
            $table->json('payload_json');
            $table->string('status', 32)->index();
            $table->unsignedInteger('attempt_count')->default(0);
            $table->string('last_error_code', 64)->nullable();
            $table->text('last_error_msg')->nullable();
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamps();

            $table->index(['event_type', 'biz_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
