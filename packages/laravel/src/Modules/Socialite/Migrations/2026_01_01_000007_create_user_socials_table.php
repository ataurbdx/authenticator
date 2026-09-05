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
        $table = config('authenticator.tables.user_socials', 'user_socials');
        $usersTable = config('authenticator.tables.users', 'users');
        $providersTable = config('authenticator.tables.social_providers', 'social_providers');

        if (!Schema::hasTable($table)) {
            Schema::create($table, function (Blueprint $table) use ($usersTable, $providersTable) {
                $table->id();
                $table->foreignId('user_id')->constrained($usersTable)->onDelete('cascade');
                
                // 🔗 Foreign Key directly to social_providers:
                $table->foreignId('social_provider_id')->constrained($providersTable)->onDelete('cascade');
                
                $table->string('provider_id', 255)->comment('Unique User ID returned by Google/GitHub');
                $table->string('email', 255)->nullable();
                $table->string('name', 255)->nullable();
                $table->string('avatar', 255)->nullable();
                $table->json('provider_data')->nullable()->comment('Full payload & access token data');
                $table->timestamps();

                // Constraints
                $table->unique(['social_provider_id', 'provider_id'], 'uniq_provider_account');
                $table->unique(['user_id', 'social_provider_id'], 'uniq_user_provider');
                $table->index('email', 'idx_social_email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = config('authenticator.tables.user_socials', 'user_socials');
        Schema::dropIfExists($table);
    }
};
