<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoSoCV extends Model
{
    use HasFactory;

    protected $table = 'ho_so_cv';
    protected $primaryKey = 'MaCV';
    public $timestamps = true;

    protected $fillable = [
        'MaTaiKhoan',
        'TenFile',
        'DuongDanFile',
        'DuLieuAITrichXuat',
        'TrangThaiXuLy',
        'TrinhDoHocVan',
        'KinhNghiem',
        'KyNang',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'MaTaiKhoan', 'MaTaiKhoan');
    }

    public function aiLogs()
    {
        return $this->hasMany(AILog::class, 'MaCV', 'MaCV');
    }

    public function hocVans()
    {
        return $this->hasMany(HocVan::class, 'MaCV', 'MaCV');
    }

    public function kinhNghiemLamViecs()
    {
        return $this->hasMany(KinhNghiemLamViec::class, 'MaCV', 'MaCV');
    }

    public function ketQuaGoiYs()
    {
        return $this->hasMany(KetQuaGoiY::class, 'MaCV', 'MaCV');
    }

    public function kyNangTrongCVs()
    {
        return $this->hasMany(KyNangTrongCV::class, 'MaCV', 'MaCV');
    }
}
