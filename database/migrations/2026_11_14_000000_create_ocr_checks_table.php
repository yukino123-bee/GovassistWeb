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
        Schema::create('ocr_checks', function (Blueprint $table) {
            $table->id();
            $table->string('status', 20);
            $table->string('method', 40);
            $table->float('score')->default(0);
            $table->unsignedInteger('duration_ms')->default(0);
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ocr_checks');
    }
};
