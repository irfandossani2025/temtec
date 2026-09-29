<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('new')->index();
            $table->text('internal_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('service_service_request', function (Blueprint $table) {
            $table->foreignId('service_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->primary(['service_request_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_service_request');
        Schema::dropIfExists('service_requests');
    }
};
