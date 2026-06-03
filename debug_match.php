<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HoSoCV;
use App\Models\TinTuyenDung;
use App\Services\AIService;

$cv = HoSoCV::find(9);
$job = TinTuyenDung::find(1);

$ai = new AIService();
echo "CV Skills IDs: " . implode(',', $cv->kyNangTrongCVs()->pluck('MaKyNang')->toArray()) . "\n";
echo "Job 1 Req Skills IDs: " . implode(',', $job->kyNangYeuCaus()->pluck('MaKyNang')->toArray()) . "\n";

foreach ($job->kyNangYeuCaus as $req) {
    echo "Checking Job Skill ID: " . $req->MaKyNang . " (" . $req->tuDienKyNang->TenKyNang . ")\n";
    $cvSkill = $cv->kyNangTrongCVs()->where('MaKyNang', $req->MaKyNang)->first();
    if ($cvSkill) {
        echo "   Match found! Level: " . $cvSkill->MucDo . "\n";
    } else {
        echo "   No match.\n";
    }
}

$score = $ai->calculateMatchScore($cv, $job);
echo "Final Score: " . $score . "\n";
