<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HoSoCV;
use App\Services\AIService;

try {
    $cv = HoSoCV::where('TrangThaiXuLy', 'Hoàn thành')->latest()->first();
    if (!$cv) {
        echo "No completed CV found\n";
        exit;
    }
    echo "Testing CV ID: " . $cv->MaCV . "\n";
    $ai = new AIService();
    $ai->suggestJobsForCV($cv);
    echo "Matching completed successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
