<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TinTuyenDung extends Model
{
    use HasFactory;

    protected $table = 'TinTuyenDung';
    protected $primaryKey = 'MaTuyenDung';
    public $timestamps = true;

    protected $fillable = [
        'TieuDe',
        'TenCongTy',
        'MoTaChiTiet',
        'LuongToiThieu',
        'LuongToiDa',
        'TrangThai',
        'DiaDiem',
        'NgayDangTuyen',
        'HanNop',
        'LoaiHinh',
        'CapBac',
    ];

    public function ketQuaGoiYs()
    {
        return $this->hasMany(KetQuaGoiY::class, 'MaTuyenDung', 'MaTuyenDung');
    }

    public function kyNangYeuCaus()
    {
        return $this->hasMany(KyNangYeuCau::class, 'MaTuyenDung', 'MaTuyenDung');
    }
}
