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
        Schema::create('ky_nang_trong_cv', function (Blueprint $table) {
            $table->unsignedInteger('MaKyNang');
            $table->unsignedInteger('MaCV');
            $table->timestamps();

            $table->primary(['MaKyNang', 'MaCV']);
            $table->foreign('MaKyNang')->references('MaKyNang')->on('tu_dien_ky_nang')->onDelete('cascade');
            $table->foreign('MaCV')->references('MaCV')->on('ho_so_cv')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ky_nang_trong_cv');
    }
};
