<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AILog extends Model
{
    use HasFactory;

    protected $table = 'AI_Log';

    protected $primaryKey = 'MaLog';

    public $timestamps = true;

    protected $fillable = [
        'MaCV',
        'ThoiGian',
        'TrangThai',
        'NoiDungLog',
    ];

    public function hoSoCV()
    {
        return $this->belongsTo(HoSoCV::class, 'MaCV', 'MaCV');
    }
}
