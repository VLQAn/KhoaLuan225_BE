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
        Schema::create('PHIM_THELOAI', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phim_id')->constrained('PHIM')->onDelete('cascade');
            $table->foreignId('theloai_id')->constrained('THELOAI')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PHIM_THELOAI');
    }
};
