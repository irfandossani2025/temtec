<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
            $table->string('company_name')->nullable()->after('name');
            $table->string('job_title')->nullable()->after('company_name');
            $table->string('phone', 50)->nullable()->after('email');
            $table->string('website')->nullable()->after('phone');
            $table->string('address')->nullable()->after('website');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('country', 100)->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'company_name', 'job_title', 'phone', 'website', 'address', 'city', 'country']);
        });
    }
};
