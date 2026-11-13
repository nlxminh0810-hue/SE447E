<?php
require_once __DIR__ . '/../bootstrap/load_env.php';
require_once __DIR__ . '/../Model/DBConfig.php';

$apiKey = getenv('OPENAI_API_KEY');
$model = getenv('OPENAI_MODEL') ?: 'gpt-6-luna';

if (!$apiKey) {
    echo json_encode(['error' => 'no_api_key']);
    exit;
}

$payloadInput = [];
$payloadInput[] = [
    'role' => 'system',
    'content' => [['type' => 'input_text', 'text' => 'You are a tester. Reply briefly.']],
];
$payloadInput[] = ['role' => 'user', 'content' => [['type' => 'input_text', 'text' => 'Say hello']]];

$ch = curl_init('https://api.openai.com/v1/responses');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'model' => $model,
        'input' => $payloadInput,
        'temperature' => 0.7,
        'max_output_tokens' => 200,
    ]),
    CURLOPT_TIMEOUT => 30,
]);

$rawResponse = curl_exec($ch);
$curlErr = curl_error($ch);
$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo json_encode(['httpCode' => $httpCode, 'curlError' => $curlErr, 'raw' => $rawResponse], JSON_UNESCAPED_UNICODE);

?>