<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetQuaGoiY extends Model
{
    use HasFactory;

    protected $table = 'ket_qua_goi_y';
    protected $primaryKey = 'MaKetQua';
    public $timestamps = true;

    protected $fillable = [
        'MaCV',
        'MaTuyenDung',
        'TyLePhuHop',
    ];

    public function hoSoCV()
    {
        return $this->belongsTo(HoSoCV::class, 'MaCV', 'MaCV');
    }

    public function tinTuyenDung()
    {
        return $this->belongsTo(TinTuyenDung::class, 'MaTuyenDung', 'MaTuyenDung');
    }
}
