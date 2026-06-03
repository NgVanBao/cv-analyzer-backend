<?php
$key = env('GEMINI_API_KEY');
// load env
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$key = env('GEMINI_API_KEY');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://generativelanguage.googleapis.com/v1beta/models?key=" . $key);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
curl_close($ch);

$data = json_decode($result, true);
if (isset($data['models'])) {
    foreach ($data['models'] as $model) {
        if (strpos($model['name'], 'gemini') !== false) {
            echo $model['name'] . "\n";
        }
    }
} else {
    print_r($data);
}
