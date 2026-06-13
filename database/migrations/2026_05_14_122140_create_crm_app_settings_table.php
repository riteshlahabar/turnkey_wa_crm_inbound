<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_app_settings', function (Blueprint $table) {
            $table->id();

            $table->string('app_name')->default("Sane's Academy");
            $table->string('app_logo')->nullable();
            $table->string('login_logo')->nullable();
            $table->string('splash_logo')->nullable();
            $table->string('default_profile_image')->nullable();

            $table->string('primary_color')->nullable();
            $table->string('support_phone')->nullable();
            $table->string('support_email')->nullable();

            $table->timestamps();
        });

        DB::table('crm_app_settings')->insert([
            'app_name' => "Sane's Academy",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_app_settings');
    }
};