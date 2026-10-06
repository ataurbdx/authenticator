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
        $table = config('authenticator.tables.settings', 'authenticator_settings');

        Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 50)->default('general')->index(); // 'verification', 'auth', '2fa', 'otp'
            $table->string('type', 20)->default('string');            // 'boolean', 'integer', 'string', 'json'
            $table->string('label', 150)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = config('authenticator.tables.settings', 'authenticator_settings');
        Schema::dropIfExists($table);
    }
};
