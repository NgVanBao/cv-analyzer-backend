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
        Schema::create('hoc_van', function (Blueprint $table) {
            $table->increments('MaHocVan');
            $table->unsignedInteger('MaCV');
            $table->string('TenTruong', 255);
            $table->string('ChuyenNganh', 255);
            $table->string('BangCap', 100);
            $table->date('ThoiGianTu');
            $table->date('ThoiGianDen')->nullable();
            $table->timestamps();

            $table->foreign('MaCV')->references('MaCV')->on('ho_so_cv')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoc_van');
    }
};
