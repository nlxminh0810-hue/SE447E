<?php
require_once __DIR__ . '/../bootstrap/load_env.php';
require_once __DIR__ . '/../Model/DBConfig.php';
require_once __DIR__ . '/../Model/UserModel.php';
require_once __DIR__ . '/../Core/Auth.php';

session_start();

$db = new Database();
$db->connect();

// Load chat controller functions (defines generate_ai_response)
require_once __DIR__ . '/../Controller/chat/index.php';

$prompt = isset($argv[1]) ? $argv[1] : 'Hello, please introduce yourself briefly as an English tutor.';
$conv = [ ['role' => 'user', 'content' => $prompt] ];

$reply = generate_ai_response($prompt, $conv);
echo json_encode(['ok' => true, 'reply' => $reply], JSON_UNESCAPED_UNICODE);

?>