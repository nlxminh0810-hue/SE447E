<?php

// Lesson Controller - Xử lý bài học

Auth::requireLogin();

$lessonCatalog = [
    1 => [
        'id' => 1,
        'course_id' => 1,
        'title' => 'Welcome & Course Overview',
        'description' => 'Làm quen với lộ trình học, cách dùng nền tảng và mục tiêu của khoá học.',
        'duration' => '12 phút',
        'type' => 'Video + Quiz',
        'progress' => 15,
        'topics' => ['Giới thiệu khoá học', 'Mục tiêu học tập', 'Cách theo dõi tiến độ'],
        'notes' => 'Hãy xem kỹ phần giới thiệu để nắm được cách học hiệu quả nhất trên ENGLISHAI.',
    ],
    2 => [
        'id' => 2,
        'course_id' => 1,
        'title' => 'Vocabulary Starter Pack',
        'description' => 'Bộ từ vựng nền tảng theo chủ đề quen thuộc và cách ghi nhớ bằng ngữ cảnh.',
        'duration' => '18 phút',
        'type' => 'Practice',
        'progress' => 40,
        'topics' => ['Từ vựng theo chủ đề', 'Flashcards', 'Câu ví dụ thực tế'],
        'notes' => 'Luyện tập 10 từ mới mỗi ngày sẽ giúp bạn tăng vốn từ rất nhanh.',
    ],
    3 => [
        'id' => 3,
        'course_id' => 1,
        'title' => 'Listening Drill: Short Talks',
        'description' => 'Luyện nghe các đoạn hội thoại ngắn với tốc độ tăng dần và câu hỏi kiểm tra nhanh.',
        'duration' => '20 phút',
        'type' => 'Audio + Test',
        'progress' => 70,
        'topics' => ['Nghe bắt keyword', 'Câu hỏi trắc nghiệm', 'Ôn tập từ vựng liên quan'],
        'notes' => 'Đừng chỉ nghe một lần, hãy nghe lại để bắt được cấu trúc câu và từ khóa.',
    ],
    4 => [
        'id' => 4,
        'course_id' => 1,
        'title' => 'Speaking Challenge',
        'description' => 'Bài luyện nói với mẫu câu thực hành, phản xạ và gợi ý phát âm.',
        'duration' => '15 phút',
        'type' => 'Speaking',
        'progress' => 90,
        'topics' => ['Phát âm', 'Trả lời ngắn', 'Luyện phản xạ'],
        'notes' => 'Hãy nói thành tiếng, ghi âm lại và nghe để điều chỉnh phát âm của mình.',
    ],
];

$lessonCatalogByCourse = [
    1 => [1, 2, 3, 4],
    2 => [1, 2, 3, 4],
    3 => [1, 2, 3, 4],
    4 => [1, 2, 3, 4],
    5 => [1, 2, 3, 4],
    6 => [1, 2, 3, 4],
    7 => [1, 2, 3, 4],
    8 => [1, 2, 3, 4],
];

function get_lesson_catalog() {
    global $lessonCatalog;
    return $lessonCatalog;
}

function normalize_db_lesson_row($row) {
    if (!$row) return null;
    $content = isset($row['content']) ? json_decode($row['content'], true) : null;
    if (is_array($content)) {
        $row['description'] = $content['description'] ?? ($row['description'] ?? '');
        $row['duration'] = $content['duration'] ?? ($row['duration'] ?? '');
        $row['type'] = $content['type'] ?? ($row['type'] ?? 'Lesson');
        $row['progress'] = $content['progress'] ?? ($row['progress'] ?? 0);
        $row['topics'] = $content['topics'] ?? [];
        $row['notes'] = $content['notes'] ?? ($row['notes'] ?? '');
    }
    return $row;
}

function get_lessons_by_course($courseId) {
    global $db;
    global $lessonCatalogByCourse;
    $catalog = get_lesson_catalog();
    require_once 'Model/LessonModel.php';
    $lessonModel = new LessonModel($db);
    $dbLessons = $lessonModel->getLessonsByCourse($courseId);
    if (!empty($dbLessons)) {
        $lessons = [];
        foreach ($dbLessons as $lesson) {
            $lessons[] = normalize_db_lesson_row($lesson);
        }
        return $lessons;
    }

    $lessonIds = isset($lessonCatalogByCourse[$courseId]) ? $lessonCatalogByCourse[$courseId] : $lessonCatalogByCourse[1];
    $lessons = [];

    foreach ($lessonIds as $lessonId) {
        if (isset($catalog[$lessonId])) {
            $lessons[] = $catalog[$lessonId];
        }
    }

    return $lessons;
}

function get_lesson_by_id($lessonId) {
    global $db;
    $catalog = get_lesson_catalog();
    require_once 'Model/LessonModel.php';
    $lessonModel = new LessonModel($db);
    $dbLesson = normalize_db_lesson_row($lessonModel->getLessonById($lessonId));
    if ($dbLesson) {
        return $dbLesson;
    }
    return isset($catalog[$lessonId]) ? $catalog[$lessonId] : reset($catalog);
}

$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch($action) {

    // Danh sách bài học của khóa học
    case 'index':
    default: {
            $courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
            $courseId = $courseId ?: 1;
            // ensure user is enrolled in this course
            require_once 'Model/CourseModel.php';
            $courseModel = new CourseModel($db);
            $userId = $_SESSION['user']['id'] ?? null;
            if (!$courseModel->isEnrolled($userId, $courseId)) {
                // redirect to checkout for this course
                $slugMap = [1=>'toeic',2=>'ielts',3=>'b1',4=>'b2',5=>'reading',6=>'listening',7=>'writing',8=>'speaking'];
                $slug = isset($slugMap[$courseId]) ? $slugMap[$courseId] : 'toeic';
                header('Location: ?controller=course&action=checkout&course=' . urlencode($slug));
                exit;
            }
            $lessons = get_lessons_by_course($courseId);
            $currentLesson = $lessons[0];
            require_once 'View/lesson/list.php';
        break;
    }

    // Chi tiết bài học
    case 'view': {
        $lessonId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $lesson = get_lesson_by_id($lessonId ?: 1);
            $lessons = get_lessons_by_course($lesson['course_id']);
            // check enrollment for this lesson's course
            require_once 'Model/CourseModel.php';
            $courseModel = new CourseModel($db);
            $userId = $_SESSION['user']['id'] ?? null;
            if (!$courseModel->isEnrolled($userId, $lesson['course_id'])) {
                $slugMap = [1=>'toeic',2=>'ielts',3=>'b1',4=>'b2',5=>'reading',6=>'listening',7=>'writing',8=>'speaking'];
                $slug = isset($slugMap[$lesson['course_id']]) ? $slugMap[$lesson['course_id']] : 'toeic';
                header('Location: ?controller=course&action=checkout&course=' . urlencode($slug));
                exit;
            }
            require_once 'View/lesson/view.php';
        break;
    }

    // Hoàn thành bài học
    case 'complete': {
        $lessonId = isset($_POST['lesson_id']) ? (int)$_POST['lesson_id'] : 0;
        // TODO: Đánh dấu bài học hoàn thành
        break;
    }
}

?>
