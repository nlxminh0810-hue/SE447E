<?php

// Test/Quiz Controller

Auth::requireLogin();

require_once 'Model/TestModel.php';

function build_db_quiz_payload($db, $testId) {
    $testModel = new TestModel($db);
    $test = $testModel->getTestById($testId);
    if (!$test) {
        return [null, []];
    }

    $questionsRaw = $testModel->getQuestionsByTest($testId);
    $questions = [];
    foreach ($questionsRaw as $index => $questionRow) {
        $options = [];
        $correctAnswer = '';
        $letterIndex = 0;
        foreach (($questionRow['answers'] ?? []) as $answerRow) {
            $letter = chr(65 + $letterIndex);
            $options[$letter] = $answerRow['content'];
            if (!empty($answerRow['is_correct'])) {
                $correctAnswer = $letter;
            }
            $letterIndex++;
        }
        $questions[(int)$questionRow['id']] = [
            'id' => (int)$questionRow['id'],
            'question' => $questionRow['content'],
            'options' => $options,
            'correctAnswer' => $correctAnswer,
        ];
    }

    $quiz = [
        'id' => (int)$test['id'],
        'lesson_id' => (int)($test['lesson_id'] ?? 0),
        'title' => $test['title'],
        'description' => 'Bài kiểm tra thực tế từ dữ liệu trong database',
        'duration_minutes' => (int)($test['duration_minutes'] ?? 15),
        'duration' => ((int)($test['duration_minutes'] ?? 15)) . ' phút',
        'totalQuestions' => count($questions),
        'passingScore' => 70,
    ];

    return [$quiz, $questions];
}

function build_db_test_list($db) {
    $testModel = new TestModel($db);
    $tests = $testModel->getAllTests();
    foreach ($tests as &$test) {
        $test['totalQuestions'] = count($testModel->getQuestionsByTest($test['id']));
        $test['duration'] = ((int)($test['duration_minutes'] ?? 0)) . ' phút';
        $test['passingScore'] = 70;
    }
    return $tests;
}

$quizzes = [
    'lesson-1' => [
        'id' => 'lesson-1',
        'lesson_id' => 1,
        'title' => 'Welcome & Course Overview - Quiz',
        'description' => 'Kiểm tra kiến thức về lộ trình học tập và cách dùng nền tảng',
        'duration' => '15 phút',
        'totalQuestions' => 20,
        'passingScore' => 70,
    ],
    'lesson-2' => [
        'id' => 'lesson-2',
        'lesson_id' => 2,
        'title' => 'Vocabulary Starter Pack - Quiz',
        'description' => 'Kiểm tra kiến thức từ vựng cơ bản',
        'duration' => '10 phút',
        'totalQuestions' => 15,
        'passingScore' => 70,
    ],
];

$questions = [
    'lesson-1' => [
        1 => [
            'id' => 1,
            'question' => 'ENGLISHAI là nền tảng học tiếng Anh như thế nào?',
            'options' => [
                'A' => 'Nền tảng truyền thống chỉ có sách giáo khoa',
                'B' => 'Nền tảng hiện đại sử dụng AI để hỗ trợ học tập',
                'C' => 'Chỉ là một ứng dụng dịch thuật',
                'D' => 'Một game học tiếng Anh đơn giản'
            ],
            'correctAnswer' => 'B',
        ],
        2 => [
            'id' => 2,
            'question' => 'Mục tiêu chính của khóa học TOEIC Intensive là gì?',
            'options' => [
                'A' => 'Học lịch sử tiếng Anh',
                'B' => 'Tăng nhanh điểm TOEIC trong thời gian ngắn',
                'C' => 'Học viết thơ tiếng Anh',
                'D' => 'Hiểu các dòng phim Hollywood'
            ],
            'correctAnswer' => 'B',
        ],
        3 => [
            'id' => 3,
            'question' => 'Làm thế nào để theo dõi tiến độ học tập trên ENGLISHAI?',
            'options' => [
                'A' => 'Không có cách nào để theo dõi',
                'B' => 'Chỉ có thể hỏi giáo viên',
                'C' => 'Thông qua phần "Progress" trên dashboard',
                'D' => 'Phải liên hệ với bộ phận chăm sóc khách hàng'
            ],
            'correctAnswer' => 'C',
        ],
        4 => [
            'id' => 4,
            'question' => 'Khoảng thời gian bao lâu để hoàn thành khóa TOEIC Intensive?',
            'options' => [
                'A' => '2 tuần',
                'B' => '5 tuần',
                'C' => '8 tuần',
                'D' => '12 tuần'
            ],
            'correctAnswer' => 'C',
        ],
        5 => [
            'id' => 5,
            'question' => 'Bài học đầu tiên của khóa học gọi là gì?',
            'options' => [
                'A' => 'Vocabulary Starter Pack',
                'B' => 'Listening Drill',
                'C' => 'Welcome & Course Overview',
                'D' => 'Speaking Challenge'
            ],
            'correctAnswer' => 'C',
        ],
        6 => [
            'id' => 6,
            'question' => 'Loại nào là một kỹ năng riêng có thể học trên ENGLISHAI?',
            'options' => [
                'A' => 'Toán học',
                'B' => 'Reading Skills',
                'C' => 'Vẽ tranh',
                'D' => 'Nấu ăn'
            ],
            'correctAnswer' => 'B',
        ],
        7 => [
            'id' => 7,
            'question' => 'Đâu là một trong những lợi ích của ENGLISHAI?',
            'options' => [
                'A' => 'Chỉ dạy lịch sử',
                'B' => 'Có bộ đề luyện thi thực chiến',
                'C' => 'Không có hỗ trợ nào',
                'D' => 'Chỉ dành cho người cao cấp'
            ],
            'correctAnswer' => 'B',
        ],
        8 => [
            'id' => 8,
            'question' => 'ChatBot AI trên ENGLISHAI có thể giúp gì?',
            'options' => [
                'A' => 'Không thể giúp gì cả',
                'B' => 'Chỉ để giải trí',
                'C' => 'Trả lời câu hỏi và hỗ trợ học tập',
                'D' => 'Chỉ để chặn người dùng'
            ],
            'correctAnswer' => 'C',
        ],
        9 => [
            'id' => 9,
            'question' => 'Để bắt đầu học một khóa học trên ENGLISHAI, bạn cần?',
            'options' => [
                'A' => 'Thanh toán ngay lập tức',
                'B' => 'Đăng nhập vào tài khoản của bạn',
                'C' => 'Không cần làm gì',
                'D' => 'Gửi email cho quản trị viên'
            ],
            'correctAnswer' => 'B',
        ],
        10 => [
            'id' => 10,
            'question' => 'Trình độ nào của IELTS Mastery?',
            'options' => [
                'A' => 'A1 Beginner',
                'B' => 'Upper-Intermediate',
                'C' => 'Advanced',
                'D' => 'Elementary'
            ],
            'correctAnswer' => 'B',
        ],
        11 => [
            'id' => 11,
            'question' => 'Tính năng nào của ENGLISHAI giúp bạn ghi chú bài học?',
            'options' => [
                'A' => 'Không có tính năng ghi chú',
                'B' => 'Có phần "Ghi chú nhanh" trong mỗi bài học',
                'C' => 'Phải dùng Word để ghi chú',
                'D' => 'Không thể ghi chú được'
            ],
            'correctAnswer' => 'B',
        ],
        12 => [
            'id' => 12,
            'question' => 'Giá tiền của khóa Reading Skills là bao nhiêu?',
            'options' => [
                'A' => '99,000₫',
                'B' => '199,000₫',
                'C' => '299,000₫',
                'D' => '399,000₫'
            ],
            'correctAnswer' => 'B',
        ],
        13 => [
            'id' => 13,
            'question' => 'Phần "Results and Answers" dùng để làm gì?',
            'options' => [
                'A' => 'Xem phim tiếng Anh',
                'B' => 'Xem kết quả bài kiểm tra và đáp án',
                'C' => 'Tải tài liệu từ internet',
                'D' => 'Chat với bạn bè'
            ],
            'correctAnswer' => 'B',
        ],
        14 => [
            'id' => 14,
            'question' => 'Có bao nhiêu khóa học chính trên ENGLISHAI?',
            'options' => [
                'A' => '4 khóa',
                'B' => '6 khóa',
                'C' => '8 khóa',
                'D' => '10 khóa'
            ],
            'correctAnswer' => 'C',
        ],
        15 => [
            'id' => 15,
            'question' => 'Speaking Challenge thuộc về bài học số mấy?',
            'options' => [
                'A' => 'Bài học 1',
                'B' => 'Bài học 2',
                'C' => 'Bài học 3',
                'D' => 'Bài học 4'
            ],
            'correctAnswer' => 'D',
        ],
        16 => [
            'id' => 16,
            'question' => 'Ai nên sử dụng khóa B1 Communicative?',
            'options' => [
                'A' => 'Người mới bắt đầu chỉ biết 5 từ',
                'B' => 'Người muốn đạt nền tảng B1 vững chắc',
                'C' => 'Chỉ cho người đã là Native Speaker',
                'D' => 'Không ai nên dùng'
            ],
            'correctAnswer' => 'B',
        ],
        17 => [
            'id' => 17,
            'question' => 'Thời lượng của khóa IELTS Mastery là bao lâu?',
            'options' => [
                'A' => '6 tuần',
                'B' => '8 tuần',
                'C' => '10 tuần',
                'D' => '12 tuần'
            ],
            'correctAnswer' => 'C',
        ],
        18 => [
            'id' => 18,
            'question' => 'Phương pháp thanh toán nào được ENGLISHAI chấp nhận?',
            'options' => [
                'A' => 'Chỉ tiền mặt',
                'B' => 'Chuyển khoản, Momo, Ngân hàng',
                'C' => 'Chỉ Bitcoin',
                'D' => 'Không chấp nhận thanh toán nào'
            ],
            'correctAnswer' => 'B',
        ],
        19 => [
            'id' => 19,
            'question' => 'Để xem chi tiết một khóa học, bạn phải?',
            'options' => [
                'A' => 'Nhấp vào nút "Xem chi tiết"',
                'B' => 'Gửi email yêu cầu',
                'C' => 'Gọi điện thoại',
                'D' => 'Không thể xem được'
            ],
            'correctAnswer' => 'A',
        ],
        20 => [
            'id' => 20,
            'question' => 'Mục tiêu chính của ENGLISHAI là gì?',
            'options' => [
                'A' => 'Kiếm tiền từ học viên',
                'B' => 'Giúp học viên học tiếng Anh hiệu quả với sự hỗ trợ của AI',
                'C' => 'Bán sách',
                'D' => 'Quảng cáo các sản phẩm'
            ],
            'correctAnswer' => 'B',
        ],
    ],
    'lesson-2' => [
        1 => [
            'id' => 1,
            'question' => '"Breakfast" có nghĩa là gì?',
            'options' => [
                'A' => 'Bữa tối',
                'B' => 'Bữa sáng',
                'C' => 'Bữa trưa',
                'D' => 'Snack'
            ],
            'correctAnswer' => 'B',
        ],
        2 => [
            'id' => 2,
            'question' => 'Chọn từ đồng nghĩa với "happy":',
            'options' => [
                'A' => 'Sad',
                'B' => 'Joyful',
                'C' => 'Angry',
                'D' => 'Tired'
            ],
            'correctAnswer' => 'B',
        ],
        3 => [
            'id' => 3,
            'question' => '"Beautiful" là tính từ chỉ?',
            'options' => [
                'A' => 'Màu sắc',
                'B' => 'Kích thước',
                'C' => 'Vẻ đẹp',
                'D' => 'Cảm giục'
            ],
            'correctAnswer' => 'C',
        ],
        4 => [
            'id' => 4,
            'question' => 'Động từ "to study" có nghĩa là?',
            'options' => [
                'A' => 'Chạy',
                'B' => 'Học',
                'C' => 'Ăn',
                'D' => 'Nói'
            ],
            'correctAnswer' => 'B',
        ],
        5 => [
            'id' => 5,
            'question' => 'Weather (thời tiết) là danh từ hay tính từ?',
            'options' => [
                'A' => 'Tính từ',
                'B' => 'Danh từ',
                'C' => 'Động từ',
                'D' => 'Trạng từ'
            ],
            'correctAnswer' => 'B',
        ],
        6 => [
            'id' => 6,
            'question' => '"Computer" dịch sang tiếng Việt là?',
            'options' => [
                'A' => 'Điện thoại',
                'B' => 'Máy tính',
                'C' => 'Máy ảnh',
                'D' => 'Máy bay'
            ],
            'correctAnswer' => 'B',
        ],
        7 => [
            'id' => 7,
            'question' => 'Số nhiều của "child" là?',
            'options' => [
                'A' => 'Childs',
                'B' => 'Childes',
                'C' => 'Children',
                'D' => 'Childern'
            ],
            'correctAnswer' => 'C',
        ],
        8 => [
            'id' => 8,
            'question' => '"Dangerous" có nghĩa là?',
            'options' => [
                'A' => 'An toàn',
                'B' => 'Nguy hiểm',
                'C' => 'Vui vẻ',
                'D' => 'Tuyệt vời'
            ],
            'correctAnswer' => 'B',
        ],
        9 => [
            'id' => 9,
            'question' => 'Danh từ "elephant" (voi) là?',
            'options' => [
                'A' => 'Động vật nhỏ',
                'B' => 'Loài chim',
                'C' => 'Động vật lớn',
                'D' => 'Loài cá'
            ],
            'correctAnswer' => 'C',
        ],
        10 => [
            'id' => 10,
            'question' => '"Difficult" là từ đối nghĩa của?',
            'options' => [
                'A' => 'Hard',
                'B' => 'Easy',
                'C' => 'Complicated',
                'D' => 'Complex'
            ],
            'correctAnswer' => 'B',
        ],
        11 => [
            'id' => 11,
            'question' => '"Blue" là tên của?',
            'options' => [
                'A' => 'Một loại hoa',
                'B' => 'Một loại động vật',
                'C' => 'Một màu sắc',
                'D' => 'Một loại đồ uống'
            ],
            'correctAnswer' => 'C',
        ],
        12 => [
            'id' => 12,
            'question' => '"Strong" có nghĩa là?',
            'options' => [
                'A' => 'Yếu',
                'B' => 'Khỏe',
                'C' => 'Béo',
                'D' => 'Gầy'
            ],
            'correctAnswer' => 'B',
        ],
        13 => [
            'id' => 13,
            'question' => 'Động từ "to cook" có nghĩa là?',
            'options' => [
                'A' => 'Ăn cơm',
                'B' => 'Nấu ăn',
                'C' => 'Rửa chén',
                'D' => 'Mua hàng'
            ],
            'correctAnswer' => 'B',
        ],
        14 => [
            'id' => 14,
            'question' => '"Book" là danh từ chỉ?',
            'options' => [
                'A' => 'Một cuốn sách',
                'B' => 'Một bút chì',
                'C' => 'Một cái bàn',
                'D' => 'Một cái ghế'
            ],
            'correctAnswer' => 'A',
        ],
        15 => [
            'id' => 15,
            'question' => '"Cold" là tính từ chỉ?',
            'options' => [
                'A' => 'Nóng',
                'B' => 'Mát',
                'C' => 'Lạnh',
                'D' => 'Ấm'
            ],
            'correctAnswer' => 'C',
        ],
    ],
];

function get_quiz($quizId) {
    global $quizzes;
    return isset($quizzes[$quizId]) ? $quizzes[$quizId] : null;
}

function get_quiz_questions($quizId) {
    global $questions;
    return isset($questions[$quizId]) ? $questions[$quizId] : [];
}

function asset_image_url($fileName) {
    $basePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
    $webPathBase = 'public/images/';
    $full = $basePath . $fileName;

    if (file_exists($full)) {
        return $webPathBase . $fileName . '?v=' . filemtime($full);
    }

    return $webPathBase . 'background.jpg?v=' . time();
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

switch($action) {
    // Danh sách các bài kiểm tra
    case 'list':
    default: {
        $tests = build_db_test_list($db);
        require_once 'View/test/list.php';
        break;
    }

    // Làm bài kiểm tra
    case 'quiz': {
        $quizId = isset($_GET['quiz']) ? trim($_GET['quiz']) : 'lesson-1';
        $quiz = null;
        $questions = [];

        if (ctype_digit($quizId)) {
            list($quiz, $questions) = build_db_quiz_payload($db, (int)$quizId);
        }

        if (!$quiz) {
            $quiz = get_quiz($quizId);
            $questions = get_quiz_questions($quizId);
        }

        if (!$quiz || empty($questions)) {
            header('Location: ?controller=test&action=list');
            exit;
        }
        require_once 'View/test/quiz.php';
        break;
    }

    // Gửi bài kiểm tra
    case 'submit': {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=test&action=list');
            exit;
        }

        $quizId = isset($_POST['quizId']) ? trim($_POST['quizId']) : 'lesson-1';
        $quiz = null;
        $questions = [];

        if (ctype_digit($quizId)) {
            list($quiz, $questions) = build_db_quiz_payload($db, (int)$quizId);
        }

        if (!$quiz) {
            $quiz = get_quiz($quizId);
            $questions = get_quiz_questions($quizId);
        }

        if (!$quiz) {
            header('Location: ?controller=test&action=list');
            exit;
        }

        // Tính điểm
        $correctCount = 0;
        $answers = [];

        foreach ($questions as $qId => $question) {
            $userAnswer = isset($_POST["q_$qId"]) ? trim($_POST["q_$qId"]) : '';
            $isCorrect = ($userAnswer === $question['correctAnswer']);

            $answers[$qId] = [
                'userAnswer' => $userAnswer,
                'correctAnswer' => $question['correctAnswer'],
                'isCorrect' => $isCorrect,
                'question' => $question['question'],
                'options' => $question['options'],
            ];

            if ($isCorrect) {
                $correctCount++;
            }
        }

        $totalQuestions = count($questions);
        $score = ($correctCount / $totalQuestions) * 100;
        $isPassed = $score >= $quiz['passingScore'];

        // Save result to session and redirect to GET result page
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // persist to DB test_results if schema and test record exist
        if (isset($db)) {
            // check if test_results table exists
            $db->execute("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'test_results'");
            $tmp = $db->getData();
            $hasTestResults = (int)($tmp['cnt'] ?? 0) > 0;

            if ($hasTestResults) {
                // use numeric DB quiz id directly, otherwise fall back to title lookup
                $testId = ctype_digit($quizId) ? (int)$quizId : 0;
                if (!$testId) {
                    $mysqli = $db->connect();
                    $titleEsc = $mysqli->real_escape_string($quiz['title']);
                    $db->execute("SELECT id FROM tests WHERE title = '$titleEsc' LIMIT 1");
                    $testRow = $db->getData();
                    $testId = !empty($testRow['id']) ? (int)$testRow['id'] : 0;
                }
                if (!empty($testId)) {
                    $userId = $_SESSION['user']['id'] ?? null;
                    $scoreVal = round($score, 2);
                    $totalQ = (int)$totalQuestions;
                    $correctA = (int)$correctCount;
                    $takenAt = date('Y-m-d H:i:s');
                    // insert result (no per-question details in schema by default)
                    $sql = "INSERT INTO test_results (user_id, test_id, score, total_questions, correct_answers, taken_at) VALUES ('{$userId}','{$testId}','{$scoreVal}','{$totalQ}','{$correctA}','{$takenAt}')";
                    $db->execute($sql);
                }
            }
        }

        $_SESSION['last_test_result'] = [
            'quizId' => $quizId,
            'quizTitle' => $quiz['title'],
            'totalQuestions' => $totalQuestions,
            'correctAnswers' => $correctCount,
            'wrongAnswers' => $totalQuestions - $correctCount - count(array_filter($answers, fn($a)=>empty($a['userAnswer']))),
            'unansweredQuestions' => count(array_filter($answers, fn($a)=>empty($a['userAnswer']))),
            'score' => $score,
            'passingScore' => $quiz['passingScore'],
            'duration' => '',
            'completedAt' => date('d/m/Y H:i'),
            'details' => $answers,
        ];

        header('Location: ?controller=test&action=result&quiz=' . urlencode($quizId));
        exit;
        break;
    }

    // Xem kết quả bài kiểm tra (demo page)
    case 'result': {
        // Load last result from session if available
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $result = null;
        $detailedQuestions = [];
        $quizId = isset($_GET['quiz']) ? trim($_GET['quiz']) : null;

        if (isset($_SESSION['last_test_result']) && is_array($_SESSION['last_test_result'])) {
            $stored = $_SESSION['last_test_result'];
            // If quiz param provided, ensure it matches stored result
            if (!$quizId || $stored['quizId'] === $quizId) {
                $result = $stored;
                // Build detailed questions using stored details
                if (isset($result['details']) && is_array($result['details'])) {
                    foreach ($result['details'] as $qId => $detail) {
                        $detailedQuestions[] = array_merge(['id' => $qId], $detail);
                    }
                }
            }
        }

        require_once 'View/test/result.php';
        break;
    }
}

?>
