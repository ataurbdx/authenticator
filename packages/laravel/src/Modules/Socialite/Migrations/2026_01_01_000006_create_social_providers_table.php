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
        $table = config('authenticator.tables.social_providers', 'social_providers');

        if (!Schema::hasTable($table)) {
            Schema::create($table, function (Blueprint $table) {
                $table->id();

                // 1. Identity & Display
                $table->string('provider', 30)->unique()->comment('Slug: google, facebook, github, apple');
                $table->string('name', 100)->comment('Display Name on buttons: "Google", "Facebook"');
                $table->string('icon', 100)->nullable()->comment('SVG or icon class (e.g. bi bi-google)');
                $table->string('button_color', 30)->nullable()->comment('Hex color e.g. #4285F4');

                // 2. OAuth Credentials (Managed 100% via Database without .env)
                $table->string('client_id', 255)->comment('OAuth App Client ID');
                $table->text('client_secret')->comment('Encrypted OAuth App Secret');
                $table->string('redirect_url', 255)->nullable()->comment('Custom callback URL (or auto-generated)');
                $table->json('scopes')->nullable()->comment('Array of scopes e.g. ["email", "profile"]');

                // 3. Provider-Specific Extras
                $table->json('settings')->nullable()->comment('Extra keys for Apple, LinkedIn, etc.');

                // 4. Status Toggle
                $table->boolean('is_active')->default(true)->comment('Admin toggle to enable/disable instantly');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = config('authenticator.tables.social_providers', 'social_providers');
        Schema::dropIfExists($table);
    }
};
