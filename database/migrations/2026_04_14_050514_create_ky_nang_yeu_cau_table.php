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
        Schema::create('ky_nang_yeu_cau', function (Blueprint $table) {
            $table->unsignedInteger('MaKyNang');
            $table->unsignedInteger('MaTuyenDung');
            $table->integer('TrongSoDiem')->default(0);
            $table->timestamps();

            $table->primary(['MaKyNang', 'MaTuyenDung']);
            $table->foreign('MaKyNang')->references('MaKyNang')->on('tu_dien_ky_nang')->onDelete('cascade');
            $table->foreign('MaTuyenDung')->references('MaTuyenDung')->on('tin_tuyen_dung')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ky_nang_yeu_cau');
    }
};
