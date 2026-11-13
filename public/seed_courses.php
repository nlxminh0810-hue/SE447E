<?php
/**
 * Quick seeder to create sample courses in the `courses` table.
 * Usage: open http://localhost:888/ENGLISHAI/public/seed_courses.php in your browser.
 * It will skip inserting a course if a row with the same title already exists.
 */

require_once __DIR__ . '/../Model/DBConfig.php';
$db = new Database();
$conn = $db->connect();

$samples = [
    ['title' => 'TOEIC Intensive', 'description' => 'Intensive preparation for TOEIC.', 'price' => 499000, 'level' => 'All levels', 'thumbnail_url' => 'courses/toeic.jpg'],
    ['title' => 'IELTS Mastery', 'description' => 'Comprehensive IELTS course.', 'price' => 899000, 'level' => 'Upper-Intermediate', 'thumbnail_url' => 'courses/ielts.jpg'],
    ['title' => 'English Speaking Club', 'description' => 'Improve your speaking with weekly practice.', 'price' => 199000, 'level' => 'Intermediate', 'thumbnail_url' => 'courses/speaking.jpg'],
    ['title' => 'Business English', 'description' => 'English for workplace communication.', 'price' => 599000, 'level' => 'Upper-Intermediate', 'thumbnail_url' => 'courses/business.jpg'],
    ['title' => 'Grammar Foundation', 'description' => 'Core grammar lessons for beginners.', 'price' => 149000, 'level' => 'Beginner', 'thumbnail_url' => 'courses/grammar.jpg'],
    ['title' => 'Reading Comprehension', 'description' => 'Strategies to improve reading.', 'price' => 249000, 'level' => 'All levels', 'thumbnail_url' => 'courses/reading.jpg'],
    ['title' => 'Vocabulary Booster', 'description' => 'Expand your active vocabulary.', 'price' => 179000, 'level' => 'All levels', 'thumbnail_url' => 'courses/vocab.jpg'],
    ['title' => 'Listening Practice Pack', 'description' => 'Real-world listening drills.', 'price' => 159000, 'level' => 'All levels', 'thumbnail_url' => 'courses/listening.jpg'],
];

echo "<h2>Seeding sample courses</h2>";
$inserted = 0;
foreach ($samples as $s) {
    $title = $conn->real_escape_string($s['title']);
    // skip if exists
    $res = $conn->query("SELECT id FROM courses WHERE title = '{$title}' LIMIT 1");
    if ($res && $res->num_rows > 0) {
        echo "<p>Skipped existing: " . htmlspecialchars($s['title']) . "</p>";
        continue;
    }
    $desc = $conn->real_escape_string($s['description']);
    $price = floatval($s['price']);
    $level = $conn->real_escape_string($s['level']);
    $thumb = $conn->real_escape_string($s['thumbnail_url']);
    $sql = "INSERT INTO courses (title, description, price, level, thumbnail_url, status, created_at, updated_at) VALUES ('{$title}','{$desc}', '{$price}', '{$level}', '{$thumb}', 1, NOW(), NOW())";
    if ($conn->query($sql)) {
        $inserted++;
        echo "<p>Inserted: " . htmlspecialchars($s['title']) . "</p>";
    } else {
        echo "<p style='color:red'>Error inserting " . htmlspecialchars($s['title']) . ": " . htmlspecialchars($conn->error) . "</p>";
    }
}

echo "<hr><p>Done. Inserted: {$inserted} new courses.</p>";
echo "<p><a href='../?controller=admin&action=courses'>Back to Admin Courses</a></p>";

?>

// dữ liệu mẫu để chạy thử, có thể xóa sau khi đã có dữ liệu thực tế trong DB