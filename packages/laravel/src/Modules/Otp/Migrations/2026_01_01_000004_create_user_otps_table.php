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
        $table = config('authenticator.tables.user_otps', 'user_otps');
        $usersTable = config('authenticator.tables.users', 'users');

        if (!Schema::hasTable($table)) {
            Schema::create($table, function (Blueprint $table) use ($usersTable) {
                $table->id();

                $table->foreignId('user_id')->nullable()->constrained($usersTable)->onDelete('cascade');
                $table->enum('type', ['email', 'phone'])->comment('Channel type');
                $table->string('contact', 255)->comment('Plain text email address or normalized phone');
                $table->json('data')->nullable()->comment('{"country_code": "+880", "phone": "1780863100"}');

                $table->string('otp_code', 255)->comment('Hashed 6-digit OTP code');
                $table->string('action', 50)->default('verify')->comment('register, login, reset_password, verify_contact');

                $table->timestamp('expires_at')->comment('Expiration timestamp (5 to 10 minutes)');
                $table->timestamp('verified_at')->nullable()->comment('When successfully verified');
                $table->tinyInteger('attempts')->default(0)->comment('Wrong attempts count');
                $table->boolean('is_used')->default(false)->comment('Prevents replay attacks');

                $table->unsignedBigInteger('visitor_id')->nullable()->comment('Security tracking ID');
                $table->json('metadata')->nullable()->comment('IP address, User-Agent, geo info');

                $table->timestamps();

                // High performance lookup indexes
                $table->index('type', 'idx_otp_type');
                $table->index('contact', 'idx_otp_contact');
                $table->index('expires_at', 'idx_otp_expires_at');
                $table->index(['contact', 'action', 'is_used'], 'idx_otp_lookup');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = config('authenticator.tables.user_otps', 'user_otps');
        Schema::dropIfExists($table);
    }
};
