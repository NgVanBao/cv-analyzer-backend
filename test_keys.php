<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$keys = [
    'KEY_1' => env('GEMINI_API_KEY'),
    'KEY_2' => env('GEMINI_API_KEY_2'),
    'KEY_3' => env('GEMINI_API_KEY_3'),
    'KEY_4' => env('GEMINI_API_KEY_4'),
];

foreach ($keys as $name => $key) {
    echo "Testing $name: " . substr($key, 0, 10) . "...\n";
    try {
        $client = \Gemini::client($key);
        $res = $client->generativeModel('gemini-flash-latest')->generateContent('Hi');
        echo "   Result: SUCCESS - " . substr($res->text(), 0, 20) . "\n";
    } catch (\Exception $e) {
        echo "   Result: FAILED - " . $e->getMessage() . "\n";
    }
}
