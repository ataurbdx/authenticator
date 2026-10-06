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
        $tableName = config('authenticator.tables.users', 'users');

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();

            // 1. Identity
            $table->string('username', 50)->unique()->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('avatar', 255)->nullable();

            // 2. Email Channel
            $table->string('email', 255)->unique()->nullable();
            $table->json('email_data')->nullable()->comment('Extra email preferences or metadata');
            $table->timestamp('email_verified_at')->nullable();

            // 3. Phone Channel
            $table->string('phone', 30)->unique()->nullable()->comment('Normalized: +8801780863100');
            $table->json('phone_data')->nullable()->comment('Structured: {"country_code": "+880", "number": "1780863100"}');
            $table->timestamp('phone_verified_at')->nullable();

            // 4. Authentication Credentials
            $table->string('password')->nullable()->comment('Nullable for social-only or OTP-only users');
            $table->string('pin', 255)->nullable()->comment('Hashed 4-6 digit security PIN');

            // 5. Account Security Toggles
            $table->boolean('two_factor')->default(false)->comment('True if 2FA is enforced on every login');
            $table->boolean('status')->default(true)->comment('Active/Banned status');

            // System Timestamps
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Fast Lookup Indexes
            $table->index(['first_name', 'last_name'], 'idx_user_names');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = config('authenticator.tables.users', 'users');
        Schema::dropIfExists($tableName);
    }
};
