<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_standards', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('crm_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_id')->nullable()->constrained('crm_standards')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('fee_amount', 12, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('standard_id');
        });

        Schema::create('crm_followup_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('crm_lead_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();

            // Keep this name as your controller uses is_admission
            $table->boolean('is_admission')->default(false);

            // Required by DashboardController for final/lost statuses
            $table->boolean('is_final')->default(false);
            $table->boolean('is_default')->default(false);

            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('crm_lead_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('crm_lead_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_priorities');
        Schema::dropIfExists('crm_lead_sources');
        Schema::dropIfExists('crm_lead_statuses');
        Schema::dropIfExists('crm_followup_types');
        Schema::dropIfExists('crm_courses');
        Schema::dropIfExists('crm_standards');
    }
};