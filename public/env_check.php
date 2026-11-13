<?php
require_once __DIR__ . '/../bootstrap/load_env.php';
header('Content-Type: application/json');
$key = getenv('OPENAI_API_KEY');
$model = getenv('OPENAI_MODEL') ?: null;
$has = $key && strlen($key) > 0;
echo json_encode([
    'hasKey' => $has ? true : false,
    'keyLength' => $has ? strlen($key) : 0,
    'model' => $model,
]);

?>
