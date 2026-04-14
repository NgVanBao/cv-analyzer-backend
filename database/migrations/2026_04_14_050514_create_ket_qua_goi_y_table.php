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
        Schema::create('ket_qua_goi_y', function (Blueprint $table) {
            $table->increments('MaKetQua');
            $table->unsignedInteger('MaCV');
            $table->unsignedInteger('MaTuyenDung');
            $table->float('TyLePhuHop');
            $table->timestamps();

            $table->foreign('MaCV')->references('MaCV')->on('ho_so_cv')->onDelete('cascade');
            $table->foreign('MaTuyenDung')->references('MaTuyenDung')->on('tin_tuyen_dung')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ket_qua_goi_y');
    }
};
