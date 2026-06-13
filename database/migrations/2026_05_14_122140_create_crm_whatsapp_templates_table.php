<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_whatsapp_templates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')->nullable()->constrained('crm_courses')->nullOnDelete();

            $table->string('title');
            $table->text('message');

            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_whatsapp_templates');
    }
};