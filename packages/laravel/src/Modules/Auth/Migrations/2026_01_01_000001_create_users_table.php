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

        // 1. If users table does NOT exist at all, create full schema
        if (!Schema::hasTable($tableName)) {
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
                $table->unsignedBigInteger('user_social_id')->nullable()->unique()->comment('Primary social profile pointer');

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
        // 2. If users table already exists, check and add missing columns individually
        else {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Identity
                if (!Schema::hasColumn($tableName, 'username')) {
                    $table->string('username', 50)->unique()->nullable()->after('id');
                }
                if (!Schema::hasColumn($tableName, 'first_name')) {
                    $table->string('first_name', 100)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'last_name')) {
                    $table->string('last_name', 100)->nullable();
                }
                if (!Schema::hasColumn($tableName, 'avatar')) {
                    $table->string('avatar', 255)->nullable();
                }

                // Email Channel
                if (!Schema::hasColumn($tableName, 'email')) {
                    $table->string('email', 255)->unique()->nullable();
                }
                if (!Schema::hasColumn($tableName, 'email_data')) {
                    $table->json('email_data')->nullable()->comment('Extra email preferences or metadata');
                }
                if (!Schema::hasColumn($tableName, 'email_verified_at')) {
                    $table->timestamp('email_verified_at')->nullable();
                }

                // Phone Channel
                if (!Schema::hasColumn($tableName, 'phone')) {
                    $table->string('phone', 30)->unique()->nullable()->comment('Normalized: +8801780863100');
                }
                if (!Schema::hasColumn($tableName, 'phone_data')) {
                    $table->json('phone_data')->nullable()->comment('Structured: {"country_code": "+880", "number": "1780863100"}');
                }
                if (!Schema::hasColumn($tableName, 'phone_verified_at')) {
                    $table->timestamp('phone_verified_at')->nullable();
                }

                // Authentication Credentials
                if (!Schema::hasColumn($tableName, 'password')) {
                    $table->string('password')->nullable()->comment('Nullable for social-only or OTP-only users');
                }
                if (!Schema::hasColumn($tableName, 'pin')) {
                    $table->string('pin', 255)->nullable()->comment('Hashed 4-6 digit security PIN');
                }
                if (!Schema::hasColumn($tableName, 'user_social_id')) {
                    $table->unsignedBigInteger('user_social_id')->nullable()->unique()->comment('Primary social profile pointer');
                }

                // Account Security Toggles
                if (!Schema::hasColumn($tableName, 'two_factor')) {
                    $table->boolean('two_factor')->default(false)->comment('True if 2FA is enforced on every login');
                }
                if (!Schema::hasColumn($tableName, 'status')) {
                    $table->boolean('status')->default(true)->comment('Active/Banned status');
                }

                // Timestamps & Soft Deletes
                if (!Schema::hasColumn($tableName, 'remember_token')) {
                    $table->rememberToken();
                }
                if (!Schema::hasColumn($tableName, 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe: No automatic dropping of table or columns to preserve existing project data.
    }
};
