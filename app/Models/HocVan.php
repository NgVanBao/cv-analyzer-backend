<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HocVan extends Model
{
    use HasFactory;

    protected $table = 'hoc_van';
    protected $primaryKey = 'MaHocVan';
    public $timestamps = true;

    protected $fillable = [
        'MaCV',
        'TenTruong',
        'ChuyenNganh',
        'BangCap',
        'ThoiGianTu',
        'ThoiGianDen',
    ];

    public function hoSoCV()
    {
        return $this->belongsTo(HoSoCV::class, 'MaCV', 'MaCV');
    }
}
