<?php
require_once __DIR__ . '/../Model/DBConfig.php';

$db = new Database();
$db->connect();

$map = [
    'toeic' => 'TOEIC Intensive',
    'ielts' => 'IELTS Mastery',
    'b1' => 'B1 Communicative',
    'b2' => 'B2 Fluency',
    'reading' => 'Reading Skills',
    'listening' => 'Listening Skills',
    'writing' => 'Writing Skills',
    'speaking' => 'Speaking Skills',
];

$out = [];
foreach ($map as $slug => $title) {
    $safeTitle = $db->escapeString($title);
    $db->execute("SELECT id FROM courses WHERE title = '{$safeTitle}' ORDER BY id DESC LIMIT 1");
    $row = $db->getData();
    $courseId = (int)($row['id'] ?? 0);
    $db->execute("SELECT COUNT(*) AS cnt FROM lessons WHERE course_id = '{$courseId}'");
    $lessonRow = $db->getData();
    $out[$slug] = [
        'title' => $title,
        'course_id' => $courseId,
        'lesson_count' => (int)($lessonRow['cnt'] ?? 0),
    ];
}

header('Content-Type: application/json');
echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

?>