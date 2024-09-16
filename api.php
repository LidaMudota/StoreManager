<?php
// Пример работы с внешним API
$apiUrl = 'https://api.example.com/data';
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

echo '<pre>';
print_r($data);
echo '</pre>';
?>
