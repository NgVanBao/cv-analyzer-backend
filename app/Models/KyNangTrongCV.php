<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KyNangTrongCV extends Model
{
    use HasFactory;

    protected $table = 'KyNangTrongCV';

    // Vô hiệu hoá auto increment vì bảng này dùng composite primary key
    public $incrementing = false;
    
    public $timestamps = true;

    protected $fillable = [
        'MaKyNang',
        'MaCV',
        'MucDo',
    ];

    public function hoSoCV()
    {
        return $this->belongsTo(HoSoCV::class, 'MaCV', 'MaCV');
    }

    public function tuDienKyNang()
    {
        return $this->belongsTo(TuDienKyNang::class, 'MaKyNang', 'MaKyNang');
    }
}
