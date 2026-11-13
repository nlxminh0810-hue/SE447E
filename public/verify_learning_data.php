<?php
require_once __DIR__ . '/../Model/DBConfig.php';

$db = new Database();
$db->connect();

$tables = ['courses', 'lessons', 'tests', 'questions', 'answers'];
$result = [];
foreach ($tables as $table) {
    $db->execute("SELECT COUNT(*) AS cnt FROM {$table}");
    $row = $db->getData();
    $result[$table] = (int)($row['cnt'] ?? 0);
}

$db->execute("SELECT id, title, teacher_id FROM courses ORDER BY id ASC LIMIT 12");
$courses = [];
while ($row = $db->getData()) {
    $courses[] = $row;
}

$db->execute("SELECT course_id, COUNT(*) AS lesson_count FROM lessons GROUP BY course_id ORDER BY course_id ASC");
$lessonCounts = [];
while ($row = $db->getData()) {
    $lessonCounts[] = $row;
}

header('Content-Type: application/json');
echo json_encode([
    'counts' => $result,
    'courses' => $courses,
    'lessonCounts' => $lessonCounts,
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

?>