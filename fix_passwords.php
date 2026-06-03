<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NguoiDung;
use Illuminate\Support\Facades\Hash;

$users = NguoiDung::all();
foreach ($users as $user) {
    $user->MatKhau = Hash::make('123456');
    $user->save();
}

echo "All passwords updated to '123456'.\n";
