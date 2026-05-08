<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetQuaGoiY extends Model
{
    use HasFactory;

    protected $table = 'KetQuaGoiY';
    protected $primaryKey = 'MaKetQua';
    public $timestamps = true;

    protected $fillable = [
        'MaCV',
        'MaTuyenDung',
        'TyLePhuHop',
        'PhanTichChiTiet',
    ];

    // Mutator: Khi lưu vào DB, giữ nguyên tiếng Việt (không mã hóa \u)
    public function setPhanTichChiTietAttribute($value)
    {
        $this->attributes['PhanTichChiTiet'] = is_array($value) 
            ? json_encode($value, JSON_UNESCAPED_UNICODE) 
            : $value;
    }

    // Accessor: Khi lấy ra code, tự động chuyển thành mảng (array)
    public function getPhanTichChiTietAttribute($value)
    {
        return json_decode($value, true);
    }

    public function hoSoCV()
    {
        return $this->belongsTo(HoSoCV::class, 'MaCV', 'MaCV');
    }

    public function tinTuyenDung()
    {
        return $this->belongsTo(TinTuyenDung::class, 'MaTuyenDung', 'MaTuyenDung');
    }
}
