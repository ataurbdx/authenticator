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
        $table = config('authenticator.tables.user_pins', 'user_pins');
        $usersTable = config('authenticator.tables.users', 'users');

        if (!Schema::hasTable($table)) {
            Schema::create($table, function (Blueprint $table) use ($usersTable) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained($usersTable)->onDelete('cascade');

                $table->boolean('pin_enabled')->default(false)->comment('Whether content locking is active');
                $table->unsignedSmallInteger('pin_timeout')->default(15)->comment('Inactivity minutes before screen lock');
                $table->tinyInteger('pin_attempts')->default(0)->comment('Failed attempts counter');
                $table->timestamp('pin_locked_until')->nullable()->comment('Lockout timestamp after max failed attempts');
                $table->timestamp('unlocked_at')->nullable()->comment('Timestamp of last successful PIN entry');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = config('authenticator.tables.user_pins', 'user_pins');
        Schema::dropIfExists($table);
    }
};
