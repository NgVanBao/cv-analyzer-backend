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
        Schema::create('ho_so_cv', function (Blueprint $table) {
            $table->increments('MaCV');
            $table->unsignedInteger('MaTaiKhoan');
            $table->string('TenFile', 255);
            $table->string('DuongDanFile', 255);
            $table->text('DuLieuAITrichXuat')->nullable();
            $table->string('TrangThaiXuLy', 50);
            $table->string('TrinhDoHocVan', 100)->nullable();
            $table->text('KinhNghiem')->nullable();
            $table->text('KyNang')->nullable();
            $table->timestamps();

            $table->foreign('MaTaiKhoan')->references('MaTaiKhoan')->on('nguoi_dung')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ho_so_cv');
    }
};
