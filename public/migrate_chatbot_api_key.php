<?php
require_once __DIR__ . '/../Model/DBConfig.php';

$db = new Database();
$db->connect();

$db->execute("SELECT column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'chatbot_configs' AND column_name = 'api_key'");
$found = $db->getData();
if (empty($found['column_name'])) {
    echo "Adding api_key column to chatbot_configs...\n";
    $db->execute("ALTER TABLE chatbot_configs ADD COLUMN api_key TEXT NULL AFTER model");
    echo "Done.\n";
} else {
    echo "api_key column already exists.\n";
}

?>