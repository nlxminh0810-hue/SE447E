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
        'video_url' => '',
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
        'video_url' => '',
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
        'video_url' => '',
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
        'video_url' => '',
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

function get_course_catalog_id($courseTitle) {
    $catalogIdByTitle = [
        'TOEIC Intensive' => 1, 'IELTS Mastery' => 2,
        'B1 Communicative' => 3, 'B2 Fluency' => 4,
        'Reading Skills' => 5, 'Listening Skills' => 6,
        'Writing Skills' => 7, 'Speaking Skills' => 8,
    ];
    return $catalogIdByTitle[$courseTitle ?? ''] ?? 1;
}

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
        if (empty($row['video_url']) && !empty($content['video_url'])) $row['video_url'] = $content['video_url'];
        $row['lesson_content'] = $content['lesson_content'] ?? '';
        if ($row['lesson_content'] === '' && empty($content['video_url']) && !empty($row['content'])) {
            $row['lesson_content'] = $row['content'];
        }
        if (isset($content['lesson_content'])) {
            $row['content'] = $content['lesson_content'];
        }
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
    $courseRow = $db->getDataWhere('courses', "id = '" . (int)$courseId . "'");
    if (empty($courseRow['title'])) return [];
    if (!empty($dbLessons)) {
        $catalogCourseId = get_course_catalog_id($courseRow['title'] ?? '');
        $lessons = [];
        foreach ($dbLessons as $lesson) {
            $normalized = normalize_db_lesson_row($lesson);
            $normalized['course_id'] = (int)$courseId;
            $normalized['order_index'] = (int)($normalized['order_index'] ?? count($lessons) + 1);
            $lessons[] = $normalized;
        }
        $supplementalLessons = ((int)$courseId === $catalogCourseId) ? [] : get_lessons_by_course($catalogCourseId);
        $existingTitles = array_map(function ($lesson) { return strtolower(trim($lesson['title'])); }, $lessons);
        $nextOrder = count($lessons) + 1;
        foreach ($supplementalLessons as $supplemental) {
            if (in_array(strtolower(trim($supplemental['title'])), $existingTitles, true)) continue;
            $supplemental['id'] = null;
            $supplemental['course_id'] = (int)$courseId;
            $supplemental['order_index'] = $nextOrder++;
            $lessons[] = $supplemental;
        }
        return $lessons;
    }

    if ((int)$courseId === 0 || empty($courseRow['title'])) return [];
    $catalogCourseId = get_course_catalog_id($courseRow['title'] ?? '');
    if ($catalogCourseId !== (int)$courseId) {
        $lessons = get_lessons_by_course($catalogCourseId);
        foreach ($lessons as &$lesson) $lesson['course_id'] = (int)$courseId;
        unset($lesson);
        return $lessons;
    }

    $lessonIds = isset($lessonCatalogByCourse[$courseId]) ? $lessonCatalogByCourse[$courseId] : $lessonCatalogByCourse[1];
    $lessons = [];

    foreach ($lessonIds as $lessonId) {
        if (isset($catalog[$lessonId])) {
            $lesson = $catalog[$lessonId];
            $lesson['course_id'] = (int)$courseId;
            $lessons[] = $lesson;
        }
    }

    return $lessons;
}

function get_course_lesson_url($courseId) {
    global $db;
    $courseId = (int)$courseId;
    $row = $db->getDataWhere('courses', "id = '{$courseId}'");
    $titleMap = [
        'TOEIC Intensive' => 'toeic', 'IELTS Mastery' => 'ielts',
        'B1 Communicative' => 'b1', 'B2 Fluency' => 'b2',
        'Reading Skills' => 'reading', 'Listening Skills' => 'listening',
        'Writing Skills' => 'writing', 'Speaking Skills' => 'speaking',
    ];
    $slug = $titleMap[$row['title'] ?? ''] ?? 'toeic';
    return '?controller=course&action=checkout&course=' . urlencode($slug);
}

function get_course_slug_for_lesson($courseId) {
    global $db;
    $courseId = (int)$courseId;
    $row = $db->getDataWhere('courses', "id = '{$courseId}'");
    $titleMap = [
        'TOEIC Intensive' => 'toeic', 'IELTS Mastery' => 'ielts',
        'B1 Communicative' => 'b1', 'B2 Fluency' => 'b2',
        'Reading Skills' => 'reading', 'Listening Skills' => 'listening',
        'Writing Skills' => 'writing', 'Speaking Skills' => 'speaking',
    ];
    return $titleMap[$row['title'] ?? ''] ?? 'toeic';
}

function get_lesson_catalog_course_id($courseId) {
    global $db;
    $courseId = (int)$courseId;
    $row = $db->getDataWhere('courses', "id = '{$courseId}'");
    return get_course_catalog_id($row['title'] ?? '');
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
    return null;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch($action) {

    // Danh sách bài học của khóa học
    case 'index':
    default: {
            $courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
            if ($courseId <= 0) {
                header('Location: ?controller=course');
                exit;
            }
            // ensure user is enrolled in this course
            require_once 'Model/CourseModel.php';
            $courseModel = new CourseModel($db);
            $userId = $_SESSION['user']['id'] ?? null;
            if (!$courseModel->isEnrolled($userId, $courseId)) {
                // redirect to checkout for this course
                header('Location: ' . get_course_lesson_url($courseId));
                exit;
            }
            $lessons = get_lessons_by_course($courseId);
            if (empty($lessons)) {
                header('Location: ?controller=course&action=detail&course=toeic');
                exit;
            }
            $currentLesson = $lessons[0];
            require_once 'View/lesson/list.php';
        break;
    }

    // Chi tiết bài học
    case 'view': {
        $lessonId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $lesson = $lessonId > 0 ? get_lesson_by_id($lessonId) : null;
        $lessons = $lesson ? get_lessons_by_course((int)$lesson['course_id']) : [];
        $matchedLesson = null;
        foreach ($lessons as $courseLesson) {
            if ((int)$courseLesson['id'] === $lessonId) { $matchedLesson = $courseLesson; break; }
        }
        if (!$matchedLesson) {
            header('Location: ?controller=course');
            exit;
        }
        $lesson = $matchedLesson;
        // check enrollment for this lesson's course
        require_once 'Model/CourseModel.php';
        $courseModel = new CourseModel($db);
        $userId = $_SESSION['user']['id'] ?? null;
        if (!$courseModel->isEnrolled($userId, $lesson['course_id'])) {
                header('Location: ' . get_course_lesson_url($lesson['course_id']));
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
