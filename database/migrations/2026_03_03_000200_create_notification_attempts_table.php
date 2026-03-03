<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignUlid('notification_id')->constrained('notifications')->cascadeOnDelete();
            $table->unsignedInteger('attempt_no');
            $table->text('target_url');
            $table->json('request_snapshot_json');
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->text('response_body_snippet')->nullable();
            $table->string('error_class', 128)->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('duration_ms')->default(0);
            $table->timestamp('created_at');

            $table->index(['notification_id', 'attempt_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_attempts');
    }
};
