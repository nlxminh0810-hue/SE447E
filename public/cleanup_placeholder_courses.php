<?php
require_once __DIR__ . '/../Model/DBConfig.php';

$db = new Database();
$db->connect();

$legacyTitles = [
    'English Speaking Club',
    'Business English',
    'Grammar Foundation',
    'Reading Comprehension',
    'Vocabulary Booster',
    'Listening Practice Pack',
];

$deleted = [];
foreach ($legacyTitles as $title) {
    $safeTitle = $db->escapeString($title);
    $db->execute("SELECT id FROM courses WHERE title = '{$safeTitle}' LIMIT 1");
    $row = $db->getData();
    if (!empty($row['id'])) {
        $courseId = (int)$row['id'];
        $db->execute("DELETE FROM courses WHERE id = '{$courseId}'");
        $deleted[] = ['id' => $courseId, 'title' => $title];
    }
}

header('Content-Type: application/json');
echo json_encode(['deleted' => $deleted], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

?>