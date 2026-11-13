<?php

// Course Controller - Xử lý khóa học

$courseCatalog = [
    'toeic' => [
        'slug' => 'toeic',
        'title' => 'TOEIC Intensive',
        'tag' => 'Exam',
        'level' => 'All levels',
        'price' => '499,000₫',
        'image' => 'toeic.jfif',
        'short_description' => 'Lộ trình luyện TOEIC tập trung vào kỹ năng nghe đọc, mẹo làm bài và tăng tốc độ xử lý đề.',
        'duration' => '8 tuần',
        'lessons' => 32,
        'students' => 1280,
        'benefits' => ['Bộ đề luyện thi thực chiến', 'Theo dõi tiến độ học tập', 'Câu hỏi trắc nghiệm mỗi buổi'],
        'outline' => ['Foundation test', 'Reading strategies', 'Listening drills', 'Mini mock tests', 'Full mock exam'],
        'payment_note' => 'Phù hợp cho học viên muốn tăng nhanh điểm TOEIC trong thời gian ngắn.',
    ],
    'ielts' => [
        'slug' => 'ielts',
        'title' => 'IELTS Mastery',
        'tag' => 'Exam',
        'level' => 'Upper-Intermediate',
        'price' => '899,000₫',
        'image' => 'ielts.jfif',
        'short_description' => 'Khoá học chuyên sâu cho IELTS với chiến lược học theo band, speaking topics và writing templates.',
        'duration' => '10 tuần',
        'lessons' => 40,
        'students' => 960,
        'benefits' => ['Band descriptors rõ ràng', 'Mẫu Writing Task 1/2', 'Bài chữa Speaking & Writing'],
        'outline' => ['Band roadmap', 'Reading practice', 'Listening practice', 'Writing task 1-2', 'Speaking mock'],
        'payment_note' => 'Khoá học phù hợp với học viên hướng tới band 6.5+.',
    ],
    'b1' => [
        'slug' => 'b1',
        'title' => 'B1 Communicative',
        'tag' => 'Level',
        'level' => 'B1',
        'price' => '299,000₫',
        'image' => 'b1.jfif',
        'short_description' => 'Lộ trình nền tảng giúp củng cố ngữ pháp, từ vựng và giao tiếp ở mức B1.',
        'duration' => '6 tuần',
        'lessons' => 24,
        'students' => 1860,
        'benefits' => ['Ngữ pháp trọng tâm', 'Bài luyện giao tiếp', 'Kiểm tra nhanh sau mỗi chủ đề'],
        'outline' => ['Vocabulary core', 'Grammar basics', 'Daily communication', 'Mid-course quiz', 'Final practice'],
        'payment_note' => 'Dành cho người học muốn đạt nền tảng B1 vững chắc.',
    ],
    'b2' => [
        'slug' => 'b2',
        'title' => 'B2 Fluency',
        'tag' => 'Level',
        'level' => 'B2',
        'price' => '399,000₫',
        'image' => 'b2.jfif',
        'short_description' => 'Nâng tốc độ phản xạ tiếng Anh và mở rộng khả năng diễn đạt ở mức B2.',
        'duration' => '7 tuần',
        'lessons' => 28,
        'students' => 1430,
        'benefits' => ['Mở rộng idioms & phrasal verbs', 'Speaking phản xạ', 'Bài test cuối chương'],
        'outline' => ['Vocabulary expansion', 'Reading fluency', 'Speaking drills', 'Writing upgrade', 'B2 final test'],
        'payment_note' => 'Phù hợp với học viên đã có nền tảng và muốn tăng độ lưu loát.',
    ],
    'reading' => [
        'slug' => 'reading',
        'title' => 'Reading Skills',
        'tag' => 'Skill',
        'level' => 'Skill Practice',
        'price' => '199,000₫',
        'image' => 'reading.jpg',
        'short_description' => 'Rèn luyện kỹ năng đọc hiểu qua bài tập từ cơ bản đến nâng cao, phù hợp mọi trình độ.',
        'duration' => '4 tuần',
        'lessons' => 16,
        'students' => 2100,
        'benefits' => ['Chiến lược skimming/scanning', 'Bài đọc đa dạng chủ đề', 'Từ vựng theo ngữ cảnh'],
        'outline' => ['Reading warm-up', 'Main idea practice', 'Detail questions', 'Timed reading', 'Review test'],
        'payment_note' => 'Khoá kỹ năng dành riêng cho học viên cần cải thiện reading nhanh chóng.',
    ],
    'listening' => [
        'slug' => 'listening',
        'title' => 'Listening Skills',
        'tag' => 'Skill',
        'level' => 'Skill Practice',
        'price' => '199,000₫',
        'image' => 'listening.jpg',
        'short_description' => 'Luyện nghe theo chủ đề với tốc độ tăng dần, tăng khả năng bắt keyword và hiểu ý chính.',
        'duration' => '4 tuần',
        'lessons' => 16,
        'students' => 1960,
        'benefits' => ['Audio luyện tập đa giọng', 'Bắt keyword hiệu quả', 'Bài tập dictation ngắn'],
        'outline' => ['Warm-up listening', 'Gap filling', 'Keyword detection', 'Dictation practice', 'Mini test'],
        'payment_note' => 'Phù hợp để cải thiện nghe phản xạ cho mọi cấp độ.',
    ],
    'writing' => [
        'slug' => 'writing',
        'title' => 'Writing Skills',
        'tag' => 'Skill',
        'level' => 'Skill Practice',
        'price' => '199,000₫',
        'image' => 'writing.jpg',
        'short_description' => 'Tập trung vào cấu trúc câu, đoạn văn và cách viết theo mục tiêu học thuật lẫn giao tiếp.',
        'duration' => '4 tuần',
        'lessons' => 16,
        'students' => 1740,
        'benefits' => ['Sửa lỗi ngữ pháp', 'Mẫu câu đa dạng', 'Hướng dẫn viết từng bước'],
        'outline' => ['Sentence building', 'Paragraph writing', 'Grammar correction', 'Task response', 'Writing review'],
        'payment_note' => 'Giúp bạn viết rõ ràng, đúng ý và tự tin hơn.',
    ],
    'speaking' => [
        'slug' => 'speaking',
        'title' => 'Speaking Skills',
        'tag' => 'Skill',
        'level' => 'Skill Practice',
        'price' => '199,000₫',
        'image' => 'speak.jpg',
        'short_description' => 'Khoá luyện phản xạ nói, phát âm, nối âm và tăng sự tự tin khi giao tiếp.',
        'duration' => '4 tuần',
        'lessons' => 16,
        'students' => 2310,
        'benefits' => ['Phát âm chuẩn hơn', 'Từ vựng giao tiếp thực tế', 'Mock speaking topics'],
        'outline' => ['Pronunciation', 'Daily speaking prompts', 'Q&A practice', 'Role-play', 'Final speaking test'],
        'payment_note' => 'Phù hợp với học viên muốn giao tiếp tự nhiên và trôi chảy hơn.',
    ],
];

function get_course_catalog() {
    global $courseCatalog;
    return $courseCatalog;
}

function asset_image_url($fileName) {
    $basePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
    $webPathBase = 'public/images/';
    $full = $basePath . $fileName;

    if (file_exists($full)) {
        return $webPathBase . $fileName . '?v=' . filemtime($full);
    }

    $fallback = $basePath . 'background.jpg';
    return $webPathBase . 'background.jpg?v=' . (file_exists($fallback) ? filemtime($fallback) : time());
}

function get_course_by_slug($slug) {
    $catalog = get_course_catalog();
    return isset($catalog[$slug]) ? $catalog[$slug] : reset($catalog);
}

function get_course_id_by_slug($slug) {
    global $db;

    $courseTitleMap = [
        'toeic' => 'TOEIC Intensive',
        'ielts' => 'IELTS Mastery',
        'b1' => 'B1 Communicative',
        'b2' => 'B2 Fluency',
        'reading' => 'Reading Skills',
        'listening' => 'Listening Skills',
        'writing' => 'Writing Skills',
        'speaking' => 'Speaking Skills',
    ];

    if (!isset($courseTitleMap[$slug])) {
        return 1;
    }

    $title = $db->escapeString($courseTitleMap[$slug]);
    $db->execute("SELECT id FROM courses WHERE title = '{$title}' ORDER BY id DESC LIMIT 1");
    $row = $db->getData();
    if (!empty($row['id'])) {
        return (int)$row['id'];
    }

    return 1;
}

$action = isset($_GET['action']) ? $_GET['action'] : 'home';

switch($action) {

    // Trang chủ / Dashboard
    case 'home':
    default: {
        require_once 'View/course/home.php';
        break;
    }

    // Danh sách tất cả khóa học
    case 'list': {
        // If user is logged in, show their enrolled courses under "My Courses"
        $courses = [];
        if (Auth::isLoggedIn()) {
            $userId = $_SESSION['user']['id'] ?? null;
            if ($userId) {
                require_once 'Model/CourseModel.php';
                $courseModel = new CourseModel($db);
                $courses = $courseModel->getEnrolledCourses($userId);
            }
        }

        // Fallback: if no enrolled courses, show catalog
        if (empty($courses)) {
            $courses = get_course_catalog();
            // Convert associative catalog to numeric-indexed list expected by view
            $courses = array_values($courses);
        }

        $viewPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'course' . DIRECTORY_SEPARATOR . 'list.php';
        if (function_exists('opcache_invalidate')) {
            @opcache_invalidate($viewPath, true);
        }
        require_once 'View/course/list.php';
        break;
    }

    // Chi tiết khóa học
    case 'detail': {
        $courseSlug = isset($_GET['course']) ? trim($_GET['course']) : 'toeic';
        $course = get_course_by_slug($courseSlug);
        // determine enrollment status for current user
        $isEnrolled = false;
        if (Auth::isLoggedIn()) {
            require_once 'Model/CourseModel.php';
            $courseModel = new CourseModel($db);
            $courseId = get_course_id_by_slug($courseSlug);
            $userId = $_SESSION['user']['id'] ?? null;
            if ($userId) {
                $isEnrolled = $courseModel->isEnrolled($userId, $courseId);
            }
        }
        require_once 'View/course/detail.php';
        break;
    }

    // Thanh toán khóa học
    case 'checkout': {
        $courseSlug = isset($_GET['course']) ? trim($_GET['course']) : 'toeic';
        $course = get_course_by_slug($courseSlug);

        // If user is not logged in, redirect to login before showing checkout
        if (!Auth::isLoggedIn()) {
            $next = '?controller=course&action=checkout&course=' . urlencode($courseSlug);
            header('Location: ?controller=auth&action=login&next=' . urlencode($next));
            exit;
        }

        // Handle simulated payment POST: create enrollment record
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Require login to enroll (redundant guard)
            Auth::requireLogin();
            $userId = $_SESSION['user']['id'] ?? null;
            if ($userId) {
                $courseId = get_course_id_by_slug($courseSlug);

                // Ensure the course exists in DB (to satisfy FK). If not, create a minimal record.
                $existingCourse = $db->getDataWhere('courses', "id = '$courseId'");
                if (empty($existingCourse)) {
                    $catalogCourse = get_course_by_slug($courseSlug);
                    $priceRaw = isset($catalogCourse['price']) ? preg_replace('/[^0-9\.]/', '', $catalogCourse['price']) : '0';
                    $priceVal = $priceRaw === '' ? 0 : (float) $priceRaw;

                    $courseData = [
                        'id' => $courseId,
                        'title' => $catalogCourse['title'] ?? $courseSlug,
                        'description' => $catalogCourse['short_description'] ?? '',
                        'price' => $priceVal,
                        'level' => $catalogCourse['level'] ?? null,
                        'thumbnail_url' => isset($catalogCourse['image']) ? 'public/images/' . $catalogCourse['image'] : null,
                        'status' => 1,
                    ];
                    // Insert minimal course record
                    $db->insert('courses', $courseData);
                }

                // Check existing enrollment
                $existing = $db->getDataWhere('course_enrollments', "user_id = '$userId' AND course_id = '$courseId'");
                if (empty($existing)) {
                    $insert = [
                        'user_id' => $userId,
                        'course_id' => $courseId,
                        'status' => 'active',
                    ];
                    $db->insert('course_enrollments', $insert);
                    // set flash message and redirect to user's course list
                    $_SESSION['flash_message'] = 'Bạn đã mua khóa học thành công. Chúc bạn học tốt!';
                    $_SESSION['flash_type'] = 'success';
                    header('Location: ?controller=course&action=list');
                    exit;
                }
            }
        }

        require_once 'View/course/checkout.php';
        break;
    }

    // Đăng ký khóa học
    case 'enroll': {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            Auth::requireLogin();
            $courseSlug = isset($_POST['course']) ? trim($_POST['course']) : 'toeic';
            header('Location: ?controller=course&action=checkout&course=' . urlencode($courseSlug));
            exit;
        }
        break;
    }
}

?>
