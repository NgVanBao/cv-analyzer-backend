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
        Schema::create('KetQuaGoiY', function (Blueprint $table) {
            $table->increments('MaKetQua');
            $table->unsignedInteger('MaCV');
            $table->unsignedInteger('MaTuyenDung');
            $table->float('TyLePhuHop');
            $table->timestamps();

            $table->foreign('MaCV')->references('MaCV')->on('HoSoCV')->onDelete('cascade');
            $table->foreign('MaTuyenDung')->references('MaTuyenDung')->on('TinTuyenDung')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KetQuaGoiY');
    }
};
