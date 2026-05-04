<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KinhNghiemLamViec extends Model
{
    use HasFactory;

    protected $table = 'KinhNghiemLamViec';
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
