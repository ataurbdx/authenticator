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
        $table = config('authenticator.tables.otp_codes', 'otp_codes');

        Schema::create($table, function (Blueprint $table) {
            $table->id();

            // 1. Recipient Contact & Delivery Channel
            $table->string('contact', 255)->comment('Recipient plain email address or normalized phone number');
            $table->string('channel', 30)->default('email')->comment('Delivery channel: email, phone');

            // 2. Hybrid Verification: Hashed OTP Code & Direct Verification Token (Link)
            $table->string('code', 255)->comment('Hashed numeric/alphanumeric OTP code');
            $table->string('token', 128)->nullable()->unique()->comment('Secure random token for direct link validation');
            $table->string('delivery_mode', 20)->default('code')->comment('Delivery presentation: code, link, or both');

            // 3. OTP Purpose (Universal scope: register, login_2fa, reset_password, order_approval, custom)
            $table->string('purpose', 50)->default('verification')->comment('Specific business purpose of this OTP');

            // 4. Identity Linking: Direct User ID + Universal Polymorphic Relation (User, Order, Admin, etc.)
            $table->unsignedBigInteger('user_id')->nullable()->index()->comment('Direct user ID pointer if applicable');
            $table->nullableMorphs('verifiable'); // creates verifiable_type and verifiable_id with composite index

            // 5. Lifecycle & Verification Status
            $table->timestamp('expires_at')->comment('Expiration timestamp');
            $table->timestamp('verified_at')->nullable()->comment('Timestamp when successfully verified');
            $table->tinyInteger('attempts')->default(0)->comment('Failed verification attempts count');
            $table->boolean('is_used')->default(false)->comment('Prevents code replay attacks');

            // 6. Security Context & Metadata
            $table->json('metadata')->nullable()->comment('IP address, User-Agent, extra contextual data');

            $table->timestamps();

            // High performance composite indexes
            $table->index(['contact', 'purpose', 'is_used'], 'idx_otp_codes_lookup');
            $table->index('expires_at', 'idx_otp_codes_expiry');
            $table->index('channel', 'idx_otp_codes_channel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = config('authenticator.tables.otp_codes', 'otp_codes');
        Schema::dropIfExists($table);
    }
};
