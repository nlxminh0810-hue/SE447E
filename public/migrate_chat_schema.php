<?php
require_once __DIR__ . '/../Model/DBConfig.php';

$db = new Database();
$conn = $db->connect();

function col_exists($conn, $table, $column) {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $sql = "SELECT COUNT(*) as cnt FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = '{$table}' AND column_name = '{$column}'";
    $res = $conn->query($sql);
    $row = $res ? $res->fetch_assoc() : ['cnt' => 0];
    return (int)($row['cnt'] ?? 0) > 0;
}

function table_exists($conn, $table) {
    $table = $conn->real_escape_string($table);
    $sql = "SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '{$table}'";
    $res = $conn->query($sql);
    $row = $res ? $res->fetch_assoc() : ['cnt' => 0];
    return (int)($row['cnt'] ?? 0) > 0;
}

if (!table_exists($conn, 'chat_sessions')) {
    die("chat_sessions table not found\n");
}

if (!col_exists($conn, 'chat_sessions', 'session_key')) {
    $conn->query("ALTER TABLE chat_sessions ADD COLUMN session_key VARCHAR(100) NULL AFTER id");
    $conn->query("UPDATE chat_sessions SET session_key = CONCAT('legacy_', id) WHERE session_key IS NULL OR session_key = ''");
    $conn->query("ALTER TABLE chat_sessions MODIFY session_key VARCHAR(100) NOT NULL");
    $conn->query("ALTER TABLE chat_sessions ADD UNIQUE KEY uq_chat_sessions_key (session_key)");
}

if (col_exists($conn, 'chat_sessions', 'user_id')) {
    $conn->query("ALTER TABLE chat_sessions MODIFY user_id INT NULL");
}

if (!col_exists($conn, 'chat_sessions', 'updated_at')) {
    $conn->query("ALTER TABLE chat_sessions ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at");
}

echo "Chat schema migrated successfully\n";
