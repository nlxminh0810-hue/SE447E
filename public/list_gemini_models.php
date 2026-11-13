<?php
require_once __DIR__ . '/../bootstrap/load_env.php';

$apiKey = getenv('GEMINI_API_KEY') ?: getenv('GOOGLE_GEMINI_API_KEY') ?: getenv('GOOGLE_API_KEY');
if (!$apiKey) {
    echo json_encode(['error' => 'no_key']);
    exit;
}

$url = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . rawurlencode($apiKey);
$ch = curl_init($url);
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30]);
$raw = curl_exec($ch);
$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

header('Content-Type: application/json');
echo json_encode(['httpCode' => $code, 'raw' => json_decode($raw, true)], JSON_UNESCAPED_UNICODE);

?>