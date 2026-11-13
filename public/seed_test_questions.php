<?php
$mysqli = new mysqli('localhost', 'root', '', 'englishai');
if ($mysqli->connect_error) {
    die('DB connection failed: ' . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

$questionsByTest = [
    1 => [
        ['content' => 'Choose the best answer: "She ____ to school every day."', 'options' => ['goes', 'go', 'went', 'going'], 'correct' => 0],
        ['content' => 'Choose the correct form: "They ____ English now."', 'options' => ['study', 'studies', 'are studying', 'studied'], 'correct' => 2],
        ['content' => 'Choose the best synonym for "beautiful".', 'options' => ['Ugly', 'Pretty', 'Bad', 'Small'], 'correct' => 1],
        ['content' => 'Choose the correct preposition: "I am interested ___ learning English."', 'options' => ['on', 'in', 'at', 'for'], 'correct' => 1],
        ['content' => 'Choose the correct sentence.', 'options' => ['She don\'t like coffee.', 'She doesn\'t like coffee.', 'She didn\'t liked coffee.', 'She not like coffee.'], 'correct' => 1],
        ['content' => 'Choose the best answer: "My brother is ___ than me."', 'options' => ['tall', 'taller', 'tallest', 'more tall'], 'correct' => 1],
        ['content' => 'Choose the correct word: "I have ___ apple."', 'options' => ['a', 'an', 'the', 'some'], 'correct' => 1],
        ['content' => 'Choose the best answer: "We ____ lunch at 12:00."', 'options' => ['have', 'has', 'having', 'is have'], 'correct' => 0],
        ['content' => 'Choose the correct question form: "____ you like tea?"', 'options' => ['Do', 'Does', 'Did', 'Is'], 'correct' => 0],
        ['content' => 'Choose the best answer: "The children are playing ___ the garden."', 'options' => ['in', 'on', 'at', 'under'], 'correct' => 0],
    ],
    2 => [
        ['content' => 'Choose the best answer: "The company has recently expanded its ____ in Asia."', 'options' => ['operation', 'operations', 'operational', 'operate'], 'correct' => 1],
        ['content' => 'Choose the correct phrase: "She is capable ___ solving the problem quickly."', 'options' => ['of', 'for', 'to', 'with'], 'correct' => 0],
        ['content' => 'Choose the best synonym for "significant".', 'options' => ['minor', 'important', 'small', 'ordinary'], 'correct' => 1],
        ['content' => 'Choose the correct form: "If I ___ more time, I would travel more."', 'options' => ['have', 'had', 'will have', 'would have'], 'correct' => 1],
        ['content' => 'Choose the best answer: "The report was written ___ a professional team."', 'options' => ['by', 'with', 'from', 'into'], 'correct' => 0],
        ['content' => 'Choose the best answer: "The meeting was postponed ___ the weather."', 'options' => ['because of', 'because', 'although', 'despite'], 'correct' => 0],
        ['content' => 'Choose the correct sentence.', 'options' => ['He has been working here since three years.', 'He has been working here for three years.', 'He has worked here since three years.', 'He is working here since three years.'], 'correct' => 1],
        ['content' => 'Choose the best answer: "Please send me the details ___ possible."', 'options' => ['as soon', 'as soon as', 'soon', 'sooner'], 'correct' => 1],
        ['content' => 'Choose the best answer: "The manager asked us ___ the presentation before noon."', 'options' => ['to complete', 'completing', 'complete', 'completed'], 'correct' => 0],
        ['content' => 'Choose the best answer: "They have decided ___ a new office in Hanoi."', 'options' => ['opening', 'to open', 'open', 'opened'], 'correct' => 1],
    ],
];

foreach ($questionsByTest as $testId => $questions) {
    $countResult = $mysqli->query("SELECT COUNT(*) AS c FROM questions WHERE test_id = {$testId}");
    $existingCount = (int) $countResult->fetch_assoc()['c'];

    if ($existingCount >= 10) {
        echo "Test {$testId} already has {$existingCount} questions.\n";
        continue;
    }

    $orderIndex = $existingCount;
    foreach ($questions as $question) {
        $content = $mysqli->real_escape_string($question['content']);
        if (!$mysqli->query("INSERT INTO questions (test_id, content, type, order_index) VALUES ({$testId}, '{$content}', 'single_choice', {$orderIndex})")) {
            echo "Failed insert question for test {$testId}: " . $mysqli->error . "\n";
            continue;
        }

        $questionId = $mysqli->insert_id;
        foreach ($question['options'] as $idx => $option) {
            $optionText = $mysqli->real_escape_string($option);
            $isCorrect = $idx === (int) $question['correct'] ? 1 : 0;
            $mysqli->query("INSERT INTO answers (question_id, content, is_correct) VALUES ({$questionId}, '{$optionText}', {$isCorrect})");
        }

        $orderIndex++;
    }
}

$mysqli->close();
echo "Quiz question seeding completed.\n";
