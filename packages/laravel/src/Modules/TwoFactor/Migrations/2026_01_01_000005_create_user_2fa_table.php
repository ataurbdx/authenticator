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
        $table = config('authenticator.tables.user_2fa', 'user_2fa');
        $usersTable = config('authenticator.tables.users', 'users');

        if (!Schema::hasTable($table)) {
            Schema::create($table, function (Blueprint $table) use ($usersTable) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained($usersTable)->onDelete('cascade');

                $table->text('totp_secret')->comment('Crypt::encrypt() of 32-character TOTP secret');
                $table->json('backup_codes')->comment('Encrypted array of single-use emergency recovery codes');
                $table->timestamp('confirmed_at')->nullable()->comment('Timestamp when user confirmed QR code setup');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = config('authenticator.tables.user_2fa', 'user_2fa');
        Schema::dropIfExists($table);
    }
};
