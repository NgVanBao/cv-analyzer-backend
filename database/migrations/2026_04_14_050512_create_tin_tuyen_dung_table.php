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
        Schema::create('tin_tuyen_dung', function (Blueprint $table) {
            $table->increments('MaTuyenDung');
            $table->string('TieuDe', 255);
            $table->string('TenCongTy', 255);
            $table->text('MoTaChiTiet');
            $table->integer('LuongToiThieu')->nullable();
            $table->integer('LuongToiDa')->nullable();
            $table->string('TrangThai', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tin_tuyen_dung');
    }
};
