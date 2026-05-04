<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KyNangYeuCau extends Model
{
    use HasFactory;

    protected $table = 'KyNangYeuCau';

    // Vô hiệu hoá auto increment vì bảng này dùng composite primary key
    public $incrementing = false;

    public $timestamps = true;

    protected $fillable = [
        'MaKyNang',
        'MaTuyenDung',
        'TrongSoDiem',
    ];

    public function tinTuyenDung()
    {
        return $this->belongsTo(TinTuyenDung::class, 'MaTuyenDung', 'MaTuyenDung');
    }

    public function tuDienKyNang()
    {
        return $this->belongsTo(TuDienKyNang::class, 'MaKyNang', 'MaKyNang');
    }
}
