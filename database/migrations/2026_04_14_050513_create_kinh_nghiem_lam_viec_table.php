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
        Schema::create('KinhNghiemLamViec', function (Blueprint $table) {
            $table->increments('MaKinhNghiem');
            $table->unsignedInteger('MaCV');
            $table->string('TenCongTy', 255);
            $table->string('ViTriCongTac', 255);
            $table->date('ThoiGianTu');
            $table->date('ThoiGianDen')->nullable();
            $table->text('MoTaChiTiet')->nullable();
            $table->timestamps();

            $table->foreign('MaCV')->references('MaCV')->on('HoSoCV')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('KinhNghiemLamViec');
    }
};
