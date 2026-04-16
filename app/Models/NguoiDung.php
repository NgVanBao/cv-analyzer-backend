<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class NguoiDung extends Authenticatable
{
    use HasFactory;

    protected $table = 'nguoi_dung';
    protected $primaryKey = 'MaTaiKhoan';
    public $timestamps = true;

    protected $fillable = [
        'HoTen',
        'Email',
        'MatKhau',
        'Vaitro',
    ];

    protected $hidden = [
        'MatKhau',
    ];

    /**
     * Override the default getAuthPassword method since our column is MatKhau
     */
    public function getAuthPassword()
    {
        return $this->MatKhau;
    }

    public function hoSoCVs()
    {
        return $this->hasMany(HoSoCV::class, 'MaTaiKhoan', 'MaTaiKhoan');
    }
}
