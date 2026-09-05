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
        $table = config('authenticator.tables.user_data', 'user_data');
        $usersTable = config('authenticator.tables.users', 'users');

        if (!Schema::hasTable($table)) {
            Schema::create($table, function (Blueprint $table) use ($usersTable) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained($usersTable)->onDelete('cascade');

                // Profile Details
                $table->text('about')->nullable()->comment('Bio / About me');
                $table->string('banner', 255)->nullable()->comment('Profile cover/banner image URL or path');
                
                // Extensible Dynamic Data
                $table->json('settings')->nullable()->comment('User preferences: theme, language, notifications, etc.');
                $table->json('metadata')->nullable()->comment('Custom key-value pairs based on project demand');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = config('authenticator.tables.user_data', 'user_data');
        Schema::dropIfExists($table);
    }
};
