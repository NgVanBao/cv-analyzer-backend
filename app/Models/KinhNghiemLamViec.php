<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KinhNghiemLamViec extends Model
{
    use HasFactory;

    protected $table = 'kinh_nghiem_lam_viec';
    protected $primaryKey = 'MaKinhNghiem';
    public $timestamps = true;

    protected $fillable = [
        'MaCV',
        'TenCongTy',
        'ViTriCongTac',
        'ThoiGianTu',
        'ThoiGianDen',
        'MoTaChiTiet',
    ];

    public function hoSoCV()
    {
        return $this->belongsTo(HoSoCV::class, 'MaCV', 'MaCV');
    }
}
