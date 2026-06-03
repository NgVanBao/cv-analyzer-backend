<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TuDienKyNang extends Model
{
    use HasFactory;

    protected $table = 'TuDienKyNang';
    protected $primaryKey = 'MaKyNang';
    public $timestamps = true;

    protected $fillable = [
        'TenKyNang',
        'LoaiKyNang',
    ];

    public function kyNangTrongCVs()
    {
        return $this->hasMany(KyNangTrongCV::class, 'MaKyNang', 'MaKyNang');
    }

    public function kyNangYeuCaus()
    {
        return $this->hasMany(KyNangYeuCau::class, 'MaKyNang', 'MaKyNang');
    }
}
