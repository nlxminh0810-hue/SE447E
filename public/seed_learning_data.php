<?php
/**
 * Seed real learning data into englishai.
 *
 * Usage:
 *   php public/seed_learning_data.php
 *   or open /public/seed_learning_data.php in the browser.
 */

require_once __DIR__ . '/../Model/DBConfig.php';

$db = new Database();
$conn = $db->connect();

function esc($conn, $value) {
    return $conn->real_escape_string((string)$value);
}

function fetchOne($conn, $sql) {
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        return $res->fetch_assoc();
    }
    return null;
}

function fetchAll($conn, $sql) {
    $res = $conn->query($sql);
    $rows = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function findTeacherId($conn) {
    $row = fetchOne($conn, "SELECT u.id FROM users u INNER JOIN user_roles ur ON ur.user_id = u.id INNER JOIN roles r ON r.id = ur.role_id WHERE r.name = 'teacher' ORDER BY u.id ASC LIMIT 1");
    if ($row && isset($row['id'])) {
        return (int)$row['id'];
    }
    $row = fetchOne($conn, "SELECT id FROM users ORDER BY id ASC LIMIT 1");
    return $row ? (int)$row['id'] : null;
}

function upsertCourse($conn, $course, $teacherId = null) {
    $title = esc($conn, $course['title']);
    $existing = fetchOne($conn, "SELECT id FROM courses WHERE title = '{$title}' LIMIT 1");
    $data = [
        'title' => $course['title'],
        'description' => $course['description'],
        'price' => $course['price'],
        'level' => $course['level'],
        'thumbnail_url' => $course['thumbnail_url'],
        'status' => 1,
    ];
    if ($teacherId !== null) {
        $data['teacher_id'] = $teacherId;
    }

    if ($existing) {
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = $key . " = '" . esc($conn, $value) . "'";
        }
        $sql = "UPDATE courses SET " . implode(', ', $sets) . " WHERE id = '" . (int)$existing['id'] . "'";
        $conn->query($sql);
        return (int)$existing['id'];
    }

    $columns = implode(', ', array_keys($data));
    $values = [];
    foreach ($data as $value) {
        $values[] = "'" . esc($conn, $value) . "'";
    }
    $sql = "INSERT INTO courses ({$columns}) VALUES (" . implode(', ', $values) . ")";
    $conn->query($sql);
    return (int)$conn->insert_id;
}

function upsertLesson($conn, $courseId, $lesson) {
    $title = esc($conn, $lesson['title']);
    $existing = fetchOne($conn, "SELECT id FROM lessons WHERE course_id = '" . (int)$courseId . "' AND title = '{$title}' LIMIT 1");

    $payload = [
        'description' => $lesson['description'],
        'duration' => $lesson['duration'],
        'type' => $lesson['type'],
        'progress' => $lesson['progress'],
        'topics' => $lesson['topics'],
        'notes' => $lesson['notes'],
    ];

    $data = [
        'course_id' => (int)$courseId,
        'title' => $lesson['title'],
        'video_url' => $lesson['video_url'],
        'content' => json_encode($payload, JSON_UNESCAPED_UNICODE),
        'order_index' => (int)$lesson['order_index'],
        'status' => 1,
    ];

    if ($existing) {
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = $key . " = '" . esc($conn, $value) . "'";
        }
        $sql = "UPDATE lessons SET " . implode(', ', $sets) . " WHERE id = '" . (int)$existing['id'] . "'";
        $conn->query($sql);
        return (int)$existing['id'];
    }

    $columns = implode(', ', array_keys($data));
    $values = [];
    foreach ($data as $value) {
        $values[] = "'" . esc($conn, $value) . "'";
    }
    $sql = "INSERT INTO lessons ({$columns}) VALUES (" . implode(', ', $values) . ")";
    $conn->query($sql);
    return (int)$conn->insert_id;
}

function upsertTest($conn, $courseId, $lessonId, $test) {
    $title = esc($conn, $test['title']);
    $existing = fetchOne($conn, "SELECT id FROM tests WHERE course_id = '" . (int)$courseId . "' AND title = '{$title}' LIMIT 1");
    $data = [
        'course_id' => (int)$courseId,
        'lesson_id' => (int)$lessonId,
        'title' => $test['title'],
        'duration_minutes' => (int)$test['duration_minutes'],
    ];

    if ($existing) {
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = $key . " = '" . esc($conn, $value) . "'";
        }
        $sql = "UPDATE tests SET " . implode(', ', $sets) . " WHERE id = '" . (int)$existing['id'] . "'";
        $conn->query($sql);
        return (int)$existing['id'];
    }

    $columns = implode(', ', array_keys($data));
    $values = [];
    foreach ($data as $value) {
        $values[] = "'" . esc($conn, $value) . "'";
    }
    $sql = "INSERT INTO tests ({$columns}) VALUES (" . implode(', ', $values) . ")";
    $conn->query($sql);
    return (int)$conn->insert_id;
}

function upsertQuestion($conn, $testId, $question) {
    $content = esc($conn, $question['content']);
    $existing = fetchOne($conn, "SELECT id FROM questions WHERE test_id = '" . (int)$testId . "' AND content = '{$content}' LIMIT 1");
    $data = [
        'test_id' => (int)$testId,
        'content' => $question['content'],
        'type' => 'single_choice',
        'order_index' => (int)$question['order_index'],
    ];

    if ($existing) {
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = $key . " = '" . esc($conn, $value) . "'";
        }
        $sql = "UPDATE questions SET " . implode(', ', $sets) . " WHERE id = '" . (int)$existing['id'] . "'";
        $conn->query($sql);
        return (int)$existing['id'];
    }

    $columns = implode(', ', array_keys($data));
    $values = [];
    foreach ($data as $value) {
        $values[] = "'" . esc($conn, $value) . "'";
    }
    $sql = "INSERT INTO questions ({$columns}) VALUES (" . implode(', ', $values) . ")";
    $conn->query($sql);
    return (int)$conn->insert_id;
}

function replaceAnswers($conn, $questionId, $answers) {
    $conn->query("DELETE FROM answers WHERE question_id = '" . (int)$questionId . "'");
    foreach ($answers as $answer) {
        $data = [
            'question_id' => (int)$questionId,
            'content' => $answer['content'],
            'is_correct' => (int)$answer['is_correct'],
        ];
        $columns = implode(', ', array_keys($data));
        $values = [];
        foreach ($data as $value) {
            $values[] = "'" . esc($conn, $value) . "'";
        }
        $sql = "INSERT INTO answers ({$columns}) VALUES (" . implode(', ', $values) . ")";
        $conn->query($sql);
    }
}

$teacherId = findTeacherId($conn);

$courses = [
    [
        'title' => 'TOEIC Intensive',
        'description' => 'Lộ trình luyện TOEIC tập trung vào từ vựng học thuật, mẹo đọc nhanh, và chiến thuật làm bài theo thời gian thực.',
        'price' => 499000,
        'level' => 'All levels',
        'thumbnail_url' => 'public/images/toeic.jfif',
        'lessons' => [
            ['title' => 'Course Orientation & TOEIC Roadmap', 'duration' => '12 phút', 'type' => 'Video + Guide', 'progress' => 10, 'topics' => ['Mục tiêu khóa học', 'Cách học hiệu quả', 'Công cụ theo dõi tiến độ'], 'notes' => 'Xem lộ trình trước để học có kế hoạch rõ ràng.', 'video_url' => 'https://example.com/video/toeic-1.mp4'],
            ['title' => 'Reading Part 5 Fast Grammar', 'duration' => '18 phút', 'type' => 'Practice', 'progress' => 35, 'topics' => ['Từ loại', 'Dấu hiệu ngữ pháp', 'Mẹo loại đáp án sai'], 'notes' => 'Đọc câu hỏi trước rồi tìm dấu hiệu ngữ pháp.', 'video_url' => 'https://example.com/video/toeic-2.mp4'],
            ['title' => 'Listening Short Conversations', 'duration' => '20 phút', 'type' => 'Audio + Quiz', 'progress' => 60, 'topics' => ['Nghe keyword', 'Bắt ý chính', 'Bẫy đáp án nghe gần giống'], 'notes' => 'Nghe 2 lần: lần 1 hiểu ý, lần 2 kiểm tra chi tiết.', 'video_url' => 'https://example.com/video/toeic-3.mp4'],
            ['title' => 'Mini Mock Test & Review', 'duration' => '25 phút', 'type' => 'Mock Test', 'progress' => 85, 'topics' => ['Canh thời gian', 'Rà soát lỗi', 'Tổng kết chiến thuật'], 'notes' => 'Làm bài mô phỏng như thi thật để quen nhịp.', 'video_url' => 'https://example.com/video/toeic-4.mp4'],
        ],
        'test' => [
            'title' => 'TOEIC Intensive - Final Check',
            'duration_minutes' => 15,
            'questions' => [
                ['content' => 'Mục tiêu chính của khóa TOEIC Intensive là gì?', 'answers' => ['Tăng nhanh điểm TOEIC', 'Học phát âm nâng cao', 'Học viết luận dài', 'Chỉ học từ vựng'], 'correct' => 0],
                ['content' => 'Khi làm Part 5, chiến lược tốt nhất là gì?', 'answers' => ['Đọc kỹ ngữ pháp và dấu hiệu câu', 'Chọn đáp án dài nhất', 'Bỏ qua câu dễ', 'Nghe audio lại nhiều lần'], 'correct' => 0],
                ['content' => 'Listening tốt hơn khi bạn tập trung vào điều gì?', 'answers' => ['Keyword và ý chính', 'Số lượng từ trong câu', 'Màu sắc của đề thi', 'Chữ cái đầu của đáp án'], 'correct' => 0],
                ['content' => 'Bài mô phỏng cuối khóa giúp học viên làm gì?', 'answers' => ['Quen nhịp làm bài thật', 'Xóa hết kiến thức cũ', 'Không cần ôn tập', 'Chỉ để giải trí'], 'correct' => 0],
            ],
        ],
    ],
    [
        'title' => 'IELTS Mastery',
        'description' => 'Khoá học IELTS theo band, tập trung Reading, Listening, Writing và Speaking với chiến lược làm bài rõ ràng.',
        'price' => 899000,
        'level' => 'Upper-Intermediate',
        'thumbnail_url' => 'public/images/ielts.jfif',
        'lessons' => [
            ['title' => 'Band Target & Study Plan', 'duration' => '14 phút', 'type' => 'Video + Guide', 'progress' => 10, 'topics' => ['Band mục tiêu', 'Lịch học', 'Cách ôn hiệu quả'], 'notes' => 'Lên band mục tiêu trước khi học để bám kế hoạch.', 'video_url' => 'https://example.com/video/ielts-1.mp4'],
            ['title' => 'Reading Strategies for Skimming', 'duration' => '18 phút', 'type' => 'Practice', 'progress' => 30, 'topics' => ['Skimming', 'Scanning', 'Matching headings'], 'notes' => 'Đọc câu hỏi trước rồi quét thông tin cần tìm.', 'video_url' => 'https://example.com/video/ielts-2.mp4'],
            ['title' => 'Writing Task 1 Templates', 'duration' => '22 phút', 'type' => 'Practice', 'progress' => 58, 'topics' => ['Overview', 'So sánh số liệu', 'Từ nối học thuật'], 'notes' => 'Học template nhưng phải linh hoạt theo biểu đồ.', 'video_url' => 'https://example.com/video/ielts-3.mp4'],
            ['title' => 'Speaking Mock Interview', 'duration' => '20 phút', 'type' => 'Speaking', 'progress' => 82, 'topics' => ['Trả lời mạch lạc', 'Tăng độ tự tin', 'Phát triển ý'], 'notes' => 'Trả lời đủ ý và mở rộng câu trả lời.', 'video_url' => 'https://example.com/video/ielts-4.mp4'],
        ],
        'test' => [
            'title' => 'IELTS Mastery - Final Check',
            'duration_minutes' => 20,
            'questions' => [
                ['content' => 'IELTS Mastery phù hợp nhất cho trình độ nào?', 'answers' => ['Beginner', 'Upper-Intermediate', 'Native only', 'A1'], 'correct' => 1],
                ['content' => 'Kỹ thuật nào quan trọng trong Reading IELTS?', 'answers' => ['Skimming và scanning', 'Chỉ dịch từng từ', 'Học thuộc đáp án', 'Bỏ qua câu hỏi'], 'correct' => 0],
                ['content' => 'Writing Task 1 cần làm rõ điều gì?', 'answers' => ['Overview và so sánh số liệu', 'Chỉ nêu cảm xúc cá nhân', 'Kể chuyện dài', 'Không cần cấu trúc'], 'correct' => 0],
                ['content' => 'Mục tiêu của speaking mock interview là gì?', 'answers' => ['Tăng sự tự tin và phát triển ý', 'Chỉ học từ vựng', 'Nghe không cần hiểu', 'Không cần luyện tập'], 'correct' => 0],
            ],
        ],
    ],
    [
        'title' => 'B1 Communicative',
        'description' => 'Xây nền tảng B1 với ngữ pháp, từ vựng giao tiếp và các tình huống nói cơ bản hằng ngày.',
        'price' => 299000,
        'level' => 'B1',
        'thumbnail_url' => 'public/images/b1.jfif',
        'lessons' => [
            ['title' => 'B1 Core Vocabulary', 'duration' => '12 phút', 'type' => 'Vocabulary', 'progress' => 12, 'topics' => ['Chủ đề hằng ngày', 'Từ vựng thông dụng', 'Phát âm từ mới'], 'notes' => 'Mỗi ngày học 10 từ mới theo ngữ cảnh.', 'video_url' => 'https://example.com/video/b1-1.mp4'],
            ['title' => 'Basic Grammar Refresh', 'duration' => '16 phút', 'type' => 'Grammar', 'progress' => 34, 'topics' => ['Thì cơ bản', 'Câu hỏi', 'Trật tự từ'], 'notes' => 'Ôn chắc ngữ pháp nền trước khi luyện nói.', 'video_url' => 'https://example.com/video/b1-2.mp4'],
            ['title' => 'Daily Communication Patterns', 'duration' => '18 phút', 'type' => 'Speaking', 'progress' => 60, 'topics' => ['Chào hỏi', 'Hỏi đường', 'Đặt lịch hẹn'], 'notes' => 'Luyện theo mẫu câu ngắn và tự nhiên.', 'video_url' => 'https://example.com/video/b1-3.mp4'],
            ['title' => 'B1 Review Test', 'duration' => '20 phút', 'type' => 'Review', 'progress' => 88, 'topics' => ['Tổng ôn', 'Kiểm tra nhanh', 'Củng cố phát âm'], 'notes' => 'Làm bài tổng kết để biết điểm mạnh và điểm yếu.', 'video_url' => 'https://example.com/video/b1-4.mp4'],
        ],
        'test' => [
            'title' => 'B1 Communicative - Final Check',
            'duration_minutes' => 15,
            'questions' => [
                ['content' => 'Khóa B1 Communicative giúp người học đạt gì?', 'answers' => ['Nền tảng B1 vững chắc', 'Band 9 IELTS', 'Chỉ học phát âm', 'Chỉ học ngữ âm'], 'correct' => 0],
                ['content' => 'Một chủ đề quan trọng trong B1 là gì?', 'answers' => ['Chào hỏi và giao tiếp hằng ngày', 'Viết luận học thuật', 'Phân tích dữ liệu', 'Dịch văn bản pháp luật'], 'correct' => 0],
                ['content' => 'Để học từ vựng hiệu quả, nên làm gì?', 'answers' => ['Học theo ngữ cảnh', 'Chỉ chép từ', 'Bỏ qua ví dụ', 'Học ngẫu nhiên'], 'correct' => 0],
                ['content' => 'Mục đích của bài review là gì?', 'answers' => ['Tổng ôn và kiểm tra nhanh', 'Làm cho dài thời gian', 'Không cần học', 'Chỉ để xem video'], 'correct' => 0],
            ],
        ],
    ],
    [
        'title' => 'B2 Fluency',
        'description' => 'Nâng độ trôi chảy với idioms, phrasal verbs, phản xạ nói và bài tập mở rộng ý tưởng.',
        'price' => 399000,
        'level' => 'B2',
        'thumbnail_url' => 'public/images/b2.jfif',
        'lessons' => [
            ['title' => 'Advanced Vocabulary Expansion', 'duration' => '14 phút', 'type' => 'Vocabulary', 'progress' => 15, 'topics' => ['Idioms', 'Phrasal verbs', 'Collocations'], 'notes' => 'Học từ đi kèm ví dụ để nhớ lâu.', 'video_url' => 'https://example.com/video/b2-1.mp4'],
            ['title' => 'Fluency Speaking Drills', 'duration' => '18 phút', 'type' => 'Speaking', 'progress' => 36, 'topics' => ['Phản xạ', 'Nói liền mạch', 'Từ nối'], 'notes' => 'Nói theo ý tưởng, không dừng quá lâu.', 'video_url' => 'https://example.com/video/b2-2.mp4'],
            ['title' => 'Reading for Meaning', 'duration' => '20 phút', 'type' => 'Reading', 'progress' => 58, 'topics' => ['Ý chính', 'Quan hệ giữa câu', 'Từ khóa ẩn'], 'notes' => 'Tập trung nghĩa tổng thể hơn là từng từ.', 'video_url' => 'https://example.com/video/b2-3.mp4'],
            ['title' => 'B2 Final Review', 'duration' => '22 phút', 'type' => 'Review', 'progress' => 84, 'topics' => ['Ôn trọng tâm', 'Rà lỗi', 'Tự đánh giá'], 'notes' => 'Ôn lại các phần còn yếu trước khi chuyển cấp.', 'video_url' => 'https://example.com/video/b2-4.mp4'],
        ],
        'test' => [
            'title' => 'B2 Fluency - Final Check',
            'duration_minutes' => 15,
            'questions' => [
                ['content' => 'B2 Fluency tập trung vào điều gì?', 'answers' => ['Phản xạ và độ trôi chảy', 'Chỉ ngữ pháp A1', 'Chỉ dịch từ', 'Chỉ nghe chép chính tả'], 'correct' => 0],
                ['content' => 'Idioms và phrasal verbs giúp gì?', 'answers' => ['Nói tự nhiên hơn', 'Mất ý nghĩa', 'Giảm vốn từ', 'Không hữu ích'], 'correct' => 0],
                ['content' => 'Khi luyện speaking B2, nên ưu tiên gì?', 'answers' => ['Nói liền mạch và dùng từ nối', 'Ngắt câu liên tục', 'Chỉ học thuộc lòng', 'Không cần phát âm'], 'correct' => 0],
                ['content' => 'Bài review cuối khóa giúp người học làm gì?', 'answers' => ['Xác định phần còn yếu', 'Xóa hết kiến thức', 'Thiếu tự tin hơn', 'Bỏ qua ôn tập'], 'correct' => 0],
            ],
        ],
    ],
    [
        'title' => 'Reading Skills',
        'description' => 'Rèn kỹ năng đọc hiểu, tìm ý chính, quét thông tin và xử lý câu hỏi theo thời gian.',
        'price' => 199000,
        'level' => 'Skill Practice',
        'thumbnail_url' => 'public/images/reading.jpg',
        'lessons' => [
            ['title' => 'Skimming for Main Idea', 'duration' => '10 phút', 'type' => 'Reading', 'progress' => 18, 'topics' => ['Đọc lướt', 'Tìm ý chính', 'Tiêu đề đoạn văn'], 'notes' => 'Đọc tiêu đề và câu mở đầu trước.', 'video_url' => 'https://example.com/video/reading-1.mp4'],
            ['title' => 'Scanning for Details', 'duration' => '15 phút', 'type' => 'Reading', 'progress' => 42, 'topics' => ['Quét số liệu', 'Tìm tên riêng', 'Từ khóa'], 'notes' => 'Xác định từ khóa trước khi quét bài đọc.', 'video_url' => 'https://example.com/video/reading-2.mp4'],
            ['title' => 'True/False/Not Given Practice', 'duration' => '18 phút', 'type' => 'Practice', 'progress' => 68, 'topics' => ['Đối chiếu thông tin', 'Bẫy suy luận', 'Không được thêm ý ngoài bài'], 'notes' => 'Chỉ chọn theo thông tin có thật trong bài.', 'video_url' => 'https://example.com/video/reading-3.mp4'],
            ['title' => 'Timed Reading Review', 'duration' => '20 phút', 'type' => 'Review', 'progress' => 90, 'topics' => ['Quản lý thời gian', 'Rà lỗi', 'Tóm tắt chiến thuật'], 'notes' => 'Làm bài dưới áp lực thời gian để tăng tốc độ.', 'video_url' => 'https://example.com/video/reading-4.mp4'],
        ],
        'test' => [
            'title' => 'Reading Skills - Final Check',
            'duration_minutes' => 12,
            'questions' => [
                ['content' => 'Skimming dùng để làm gì?', 'answers' => ['Tìm ý chính nhanh', 'Dịch từng từ', 'Học thuộc bài', 'Nghe audio'], 'correct' => 0],
                ['content' => 'Scanning dùng để làm gì?', 'answers' => ['Tìm thông tin cụ thể', 'Tìm cảm xúc của tác giả', 'Đọc chậm từng chữ', 'Viết lại bài'], 'correct' => 0],
                ['content' => 'True/False/Not Given yêu cầu điều gì?', 'answers' => ['Chỉ dựa vào thông tin trong bài', 'Suy đoán thêm', 'Tự thêm ý kiến', 'Bỏ qua đoạn khó'], 'correct' => 0],
                ['content' => 'Luyện đọc theo thời gian giúp gì?', 'answers' => ['Tăng tốc độ và kiểm soát thời gian', 'Làm bài lâu hơn', 'Giảm tập trung', 'Không cần ôn'], 'correct' => 0],
            ],
        ],
    ],
    [
        'title' => 'Listening Skills',
        'description' => 'Luyện nghe chủ động, bắt keyword, nhận diện số liệu và hiểu ý chính trong nhiều ngữ cảnh.',
        'price' => 199000,
        'level' => 'Skill Practice',
        'thumbnail_url' => 'public/images/listening.jpg',
        'lessons' => [
            ['title' => 'Keyword Recognition', 'duration' => '12 phút', 'type' => 'Listening', 'progress' => 15, 'topics' => ['Keyword', 'Âm nối', 'Tốc độ nghe'], 'notes' => 'Đừng cố nghe từng từ, hãy nghe ý chính.', 'video_url' => 'https://example.com/video/listening-1.mp4'],
            ['title' => 'Numbers and Dates', 'duration' => '14 phút', 'type' => 'Listening', 'progress' => 34, 'topics' => ['Số đếm', 'Ngày tháng', 'Giờ giấc'], 'notes' => 'Ghi chú nhanh các con số bạn nghe được.', 'video_url' => 'https://example.com/video/listening-2.mp4'],
            ['title' => 'Short Conversations', 'duration' => '16 phút', 'type' => 'Practice', 'progress' => 60, 'topics' => ['Hội thoại ngắn', 'Vai trò người nói', 'Suy luận từ ngữ cảnh'], 'notes' => 'Xác định ai đang nói và mục đích cuộc trò chuyện.', 'video_url' => 'https://example.com/video/listening-3.mp4'],
            ['title' => 'Listening Review Test', 'duration' => '20 phút', 'type' => 'Review', 'progress' => 88, 'topics' => ['Tổng kết', 'Rà lỗi', 'Tăng độ chính xác'], 'notes' => 'Nghe lại phần sai để cải thiện phản xạ.', 'video_url' => 'https://example.com/video/listening-4.mp4'],
        ],
        'test' => [
            'title' => 'Listening Skills - Final Check',
            'duration_minutes' => 12,
            'questions' => [
                ['content' => 'Listening Skills giúp cải thiện điều gì?', 'answers' => ['Bắt keyword và ý chính', 'Chỉ đọc hiểu', 'Chỉ viết bài', 'Chỉ ngữ pháp'], 'correct' => 0],
                ['content' => 'Khi nghe số liệu, bạn nên làm gì?', 'answers' => ['Ghi chú nhanh', 'Bỏ qua', 'Đọc lại đề thi', 'Chỉ đoán'], 'correct' => 0],
                ['content' => 'Trong hội thoại ngắn, nên xác định điều gì?', 'answers' => ['Vai trò và mục đích người nói', 'Màu áo người nói', 'Cấu trúc câu trong đầu', 'Không cần nghe'], 'correct' => 0],
                ['content' => 'Luyện nghe lại phần sai giúp gì?', 'answers' => ['Cải thiện phản xạ và độ chính xác', 'Giảm điểm', 'Mất động lực', 'Không hữu ích'], 'correct' => 0],
            ],
        ],
    ],
    [
        'title' => 'Writing Skills',
        'description' => 'Phát triển cách viết rõ ràng, đúng ngữ pháp và có cấu trúc từ câu đơn đến đoạn văn.',
        'price' => 199000,
        'level' => 'Skill Practice',
        'thumbnail_url' => 'public/images/writing.jpg',
        'lessons' => [
            ['title' => 'Sentence Building Basics', 'duration' => '12 phút', 'type' => 'Writing', 'progress' => 14, 'topics' => ['Câu đơn', 'Chủ-vị', 'Trật tự từ'], 'notes' => 'Viết câu ngắn, đúng trước rồi mới mở rộng.', 'video_url' => 'https://example.com/video/writing-1.mp4'],
            ['title' => 'Paragraph Structure', 'duration' => '16 phút', 'type' => 'Writing', 'progress' => 36, 'topics' => ['Topic sentence', 'Supporting ideas', 'Conclusion'], 'notes' => 'Mỗi đoạn nên có ý chính rõ ràng.', 'video_url' => 'https://example.com/video/writing-2.mp4'],
            ['title' => 'Grammar Correction Practice', 'duration' => '18 phút', 'type' => 'Practice', 'progress' => 62, 'topics' => ['Thì', 'Mạo từ', 'Sự hòa hợp chủ-vị'], 'notes' => 'Sửa lỗi lặp lại sẽ giúp tiến bộ nhanh.', 'video_url' => 'https://example.com/video/writing-3.mp4'],
            ['title' => 'Writing Review Test', 'duration' => '20 phút', 'type' => 'Review', 'progress' => 90, 'topics' => ['Tự chỉnh lỗi', 'Rà cấu trúc', 'Cải thiện mạch văn'], 'notes' => 'Đọc lại bài và đánh dấu lỗi ngữ pháp.', 'video_url' => 'https://example.com/video/writing-4.mp4'],
        ],
        'test' => [
            'title' => 'Writing Skills - Final Check',
            'duration_minutes' => 12,
            'questions' => [
                ['content' => 'Khi viết, điều gì quan trọng nhất ở giai đoạn đầu?', 'answers' => ['Viết đúng cấu trúc câu', 'Viết thật dài', 'Không cần kiểm tra', 'Dùng từ khó nhất'], 'correct' => 0],
                ['content' => 'Một đoạn văn tốt cần có gì?', 'answers' => ['Câu chủ đề và ý hỗ trợ', 'Chỉ một câu', 'Nhiều dấu chấm than', 'Không cần liên kết'], 'correct' => 0],
                ['content' => 'Sửa lỗi ngữ pháp giúp gì?', 'answers' => ['Tăng độ chính xác', 'Làm bài dài hơn', 'Giảm điểm', 'Không cần thiết'], 'correct' => 0],
                ['content' => 'Mục đích của review test là gì?', 'answers' => ['Cải thiện mạch văn và lỗi còn lại', 'Viết lại từ đầu mọi thứ', 'Không phải làm', 'Chỉ để xem từ vựng'], 'correct' => 0],
            ],
        ],
    ],
    [
        'title' => 'Speaking Skills',
        'description' => 'Luyện phản xạ nói, phát âm và tự tin giao tiếp theo chủ đề thực tế.',
        'price' => 199000,
        'level' => 'Skill Practice',
        'thumbnail_url' => 'public/images/speak.jpg',
        'lessons' => [
            ['title' => 'Pronunciation Fundamentals', 'duration' => '12 phút', 'type' => 'Speaking', 'progress' => 16, 'topics' => ['Âm cuối', 'Trọng âm', 'Ngắt câu'], 'notes' => 'Nói chậm rõ trước, sau đó tăng tốc dần.', 'video_url' => 'https://example.com/video/speaking-1.mp4'],
            ['title' => 'Daily Topics Response', 'duration' => '15 phút', 'type' => 'Speaking', 'progress' => 38, 'topics' => ['Gia đình', 'Công việc', 'Sở thích'], 'notes' => 'Trả lời đủ ý rồi mở rộng thêm một câu.', 'video_url' => 'https://example.com/video/speaking-2.mp4'],
            ['title' => 'Role-play Situations', 'duration' => '18 phút', 'type' => 'Practice', 'progress' => 63, 'topics' => ['Mua sắm', 'Hỏi đường', 'Đặt lịch'], 'notes' => 'Luyện tình huống đời thực để phản xạ tốt hơn.', 'video_url' => 'https://example.com/video/speaking-3.mp4'],
            ['title' => 'Speaking Review Test', 'duration' => '20 phút', 'type' => 'Review', 'progress' => 90, 'topics' => ['Tự ghi âm', 'Tự nghe lại', 'Chỉnh phát âm'], 'notes' => 'Tự nghe lại câu trả lời để sửa điểm yếu.', 'video_url' => 'https://example.com/video/speaking-4.mp4'],
        ],
        'test' => [
            'title' => 'Speaking Skills - Final Check',
            'duration_minutes' => 12,
            'questions' => [
                ['content' => 'Speaking Skills tập trung vào điều gì?', 'answers' => ['Phản xạ và phát âm', 'Viết luận', 'Tính toán', 'Dịch tài liệu'], 'correct' => 0],
                ['content' => 'Khi trả lời câu hỏi nói, nên làm gì?', 'answers' => ['Trả lời đủ ý và mở rộng', 'Chỉ nói 1 từ', 'Nghe rồi im lặng', 'Học thuộc nguyên văn'], 'correct' => 0],
                ['content' => 'Role-play giúp ích gì?', 'answers' => ['Luyện tình huống thực tế', 'Giảm vốn từ', 'Không cần giao tiếp', 'Chỉ đọc sách'], 'correct' => 0],
                ['content' => 'Tự nghe lại bài nói giúp gì?', 'answers' => ['Sửa phát âm và nhịp nói', 'Không có ích', 'Làm mất tự tin', 'Chỉ để chấm điểm'], 'correct' => 0],
            ],
        ],
    ],
];

echo "<h2>Seeding learning data</h2>";
$summary = [
    'courses' => 0,
    'lessons' => 0,
    'tests' => 0,
    'questions' => 0,
    'answers' => 0,
];

foreach ($courses as $course) {
    $courseId = upsertCourse($conn, $course, $teacherId);
    $summary['courses']++;
    echo '<p>Course ready: ' . htmlspecialchars($course['title']) . ' (ID ' . (int)$courseId . ')</p>';

    foreach ($course['lessons'] as $index => $lesson) {
        $lessonId = upsertLesson($conn, $courseId, [
            'title' => $lesson['title'],
            'description' => $lesson['notes'],
            'duration' => $lesson['duration'],
            'type' => $lesson['type'],
            'progress' => $lesson['progress'],
            'topics' => $lesson['topics'],
            'notes' => $lesson['notes'],
            'video_url' => $lesson['video_url'],
            'order_index' => $index + 1,
        ]);
        $summary['lessons']++;
        echo '<p style="margin-left:20px">Lesson ready: ' . htmlspecialchars($lesson['title']) . ' (ID ' . (int)$lessonId . ')</p>';
    }

    $firstLesson = fetchOne($conn, "SELECT id FROM lessons WHERE course_id = '" . (int)$courseId . "' ORDER BY order_index ASC, id ASC LIMIT 1");
    $lessonId = $firstLesson ? (int)$firstLesson['id'] : null;
    $testId = upsertTest($conn, $courseId, $lessonId, $course['test']);
    $summary['tests']++;
    echo '<p style="margin-left:20px">Test ready: ' . htmlspecialchars($course['test']['title']) . ' (ID ' . (int)$testId . ')</p>';

    foreach ($course['test']['questions'] as $order => $question) {
        $questionId = upsertQuestion($conn, $testId, [
            'content' => $question['content'],
            'order_index' => $order + 1,
        ]);
        $summary['questions']++;

        $answers = [];
        foreach ($question['answers'] as $idx => $answerText) {
            $answers[] = [
                'content' => $answerText,
                'is_correct' => $idx === (int)$question['correct'] ? 1 : 0,
            ];
        }
        replaceAnswers($conn, $questionId, $answers);
        $summary['answers'] += count($answers);
        echo '<p style="margin-left:40px">Question ready: ' . htmlspecialchars($question['content']) . '</p>';
    }
}

echo '<hr>';
echo '<p>Done.</p>';
echo '<ul>';
echo '<li>Courses processed: ' . (int)$summary['courses'] . '</li>';
echo '<li>Lessons processed: ' . (int)$summary['lessons'] . '</li>';
echo '<li>Tests processed: ' . (int)$summary['tests'] . '</li>';
echo '<li>Questions processed: ' . (int)$summary['questions'] . '</li>';
echo '<li>Answers processed: ' . (int)$summary['answers'] . '</li>';
echo '</ul>';
echo '<p><a href="../?controller=course&action=home">Go to Home</a></p>';

