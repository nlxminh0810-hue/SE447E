<?php
require_once __DIR__ . '/../bootstrap/load_env.php';

$apiKey = getenv('GEMINI_API_KEY') ?: getenv('GOOGLE_GEMINI_API_KEY') ?: getenv('GOOGLE_API_KEY');
$model = getenv('GEMINI_MODEL') ?: 'gemini-3.6-flash';

if (!$apiKey) {
    echo json_encode(['error' => 'no_gemini_key']);
    exit;
}

$payload = [
    'system_instruction' => [ 'parts' => [['text' => 'You are a tester. Reply with OK']]],
    'contents' => [ ['role' => 'user', 'parts' => [['text' => 'Hello from test']]] ],
    'generationConfig' => ['temperature' => 0.2, 'maxOutputTokens' => 200],
];

$url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . rawurlencode($apiKey);
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [ 'Content-Type: application/json' ],
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_TIMEOUT => 30,
]);

$raw = curl_exec($ch);
$err = curl_error($ch);
$code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo json_encode(['httpCode' => $code, 'curlError' => $err, 'raw' => $raw], JSON_UNESCAPED_UNICODE);

?>