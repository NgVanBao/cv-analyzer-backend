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
        Schema::create('TinTuyenDung', function (Blueprint $table) {
            $table->increments('MaTuyenDung');
            $table->string('TieuDe', 255);
            $table->string('TenCongTy', 255);
            $table->text('MoTaChiTiet');
            $table->integer('LuongToiThieu')->nullable();
            $table->integer('LuongToiDa')->nullable();
            $table->string('TrangThai', 50);
            $table->string('DiaDiem', 255)->nullable();
            $table->date('NgayDangTuyen')->nullable();
            $table->date('HanNop')->nullable();
            $table->string('LoaiHinh', 100)->nullable();
            $table->string('CapBac', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TinTuyenDung');
    }
};
