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
        $table = config('authenticator.tables.otp_channels', 'otp_channels');

        Schema::create($table, function (Blueprint $table) {
            $table->id();

            // 1. Optional configuration name identifier
            $table->string('name', 50)->nullable()->comment('Optional configuration name identifier e.g. "authenticator", "default", "marketing_sms"');

            // 2. Channel & Provider Type
            $table->string('type', 30)->comment('email, phone');
            $table->string('provider', 50)->nullable()->comment('smtp, twilio, sslwireless, custom_http');

            // 3. Standalone JSON Credentials (Priority 1: direct JSON storage on this table)
            $table->json('credentials')->nullable()->comment('Priority 1: Direct JSON credentials (host, port, username, password, api_key)');

            // 4. Polymorphic Gateway Connection (Priority 2: external model e.g. SmtpSetting, SmsGateway)
            $table->nullableMorphs('gateway');

            // 5. Status & Extra Settings
            $table->boolean('status')->default(true)->comment('Active/Inactive toggle');
            $table->json('settings')->nullable()->comment('Custom endpoint URL, parameters, message templates, rate limits');

            $table->timestamps();

            // Fast Lookup Indexes
            $table->index(['name', 'status'], 'idx_otp_channel_name_status');
            $table->index(['type', 'status'], 'idx_otp_channel_type_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = config('authenticator.tables.otp_channels', 'otp_channels');
        Schema::dropIfExists($table);
    }
};
