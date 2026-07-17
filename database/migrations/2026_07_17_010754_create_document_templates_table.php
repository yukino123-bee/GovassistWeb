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
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('government_services')->onDelete('cascade');
            $table->foreignId('requirement_id')->constrained('service_requirements')->onDelete('cascade');
            $table->string('file_path');
            $table->string('name_en');
            $table->string('name_ceb');
            $table->text('description_en')->nullable();
            $table->text('description_ceb')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
