<?php
$json = json_decode(file_get_contents('https://generativelanguage.googleapis.com/v1beta/models?key=AIzaSyDB54U7XF_bm6CcMx_xKKqj0Oamy_5ZIHI'), true);
foreach ($json['models'] as $m) echo $m['name'] . PHP_EOL;
