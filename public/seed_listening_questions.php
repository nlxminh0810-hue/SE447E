<?php
$mysqli = new mysqli('localhost', 'root', '', 'englishai');
if ($mysqli->connect_error) {
    die('DB connection failed: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

typefunction_exists:
$questions = [
    [
        'content' => 'What should you do when you hear the phrase "Please repeat that" in a listening conversation?',
        'options' => ['Answer immediately', 'Ask for clarification', 'Ignore the speaker', 'Change the subject'],
        'correct' => 1,
    ],
    [
        'content' => 'In listening practice, the word "main idea" usually refers to:',
        'options' => ['A small detail only', 'The author\'s name', 'The central point of the passage', 'The last sentence'],
        'correct' => 2,
    ],
    [
        'content' => 'If you hear someone say "I can\'t catch what he said", they mean:',
        'options' => ['They understood clearly', 'They want to record it', 'They did not hear it well', 'They liked the speaker'],
        'correct' => 2,
    ],
    [
        'content' => 'What is the best strategy when listening for keywords?',
        'options' => ['Read every word silently', 'Look for specific important words', 'Listen only to the first sentence', 'Skip the recording'],
        'correct' => 1,
    ],
    [
        'content' => 'When a speaker uses the word "however", they are often:',
        'options' => ['Introducing a contrast', 'Giving more examples', 'Changing the topic completely', 'Asking a question'],
        'correct' => 0,
    ],
    [
        'content' => 'What is the meaning of "background noise" in a listening test?',
        'options' => ['The main topic', 'Unwanted sounds that make listening harder', 'The language being used', 'The final answer'],
        'correct' => 1,
    ],
];

$testId = 6;
$existing = $mysqli->query("SELECT COUNT(*) AS c FROM questions WHERE test_id = {$testId}")->fetch_assoc()['c'];
$orderIndex = (int)$existing;

foreach ($questions as $question) {
    $content = $mysqli->real_escape_string($question['content']);
    $sql = "INSERT INTO questions (test_id, content, type, order_index) VALUES ({$testId}, '{$content}', 'single_choice', {$orderIndex})";
    if (!$mysqli->query($sql)) {
        echo "Insert question failed: " . $mysqli->error . "\n";
        continue;
    }
    $questionId = $mysqli->insert_id;
    foreach ($question['options'] as $idx => $option) {
        $opt = $mysqli->real_escape_string($option);
        $isCorrect = $idx === (int)$question['correct'] ? 1 : 0;
        $mysqli->query("INSERT INTO answers (question_id, content, is_correct) VALUES ({$questionId}, '{$opt}', {$isCorrect})");
    }
    $orderIndex++;
}

echo "Listening quiz extended. Now has " . ($orderIndex) . " questions.\n";
$mysqli->close();
