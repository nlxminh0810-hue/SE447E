<?php

// Admin Controller - Quản lý toàn bộ hệ thống

Auth::requireAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Load models
require_once 'Model/UserModel.php';
require_once 'Model/CourseModel.php';
require_once 'Model/TestModel.php';

$userModel = new UserModel($db);
$courseModel = new CourseModel($db);
$testModel = new TestModel($db);

// Dashboard statistics from DB
$adminStats = [
    'total_users' => (int)$db->countData('users'),
    'active_users' => (int)$db->countData('users', "status = 1"),
    'total_courses' => (int)$db->countData('courses'),
    'published_courses' => (int)$db->countData('courses', "status = 1"),
    'new_signups_week' => (int)$db->countData('users', "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"),
    'completion_rate' => 0, // placeholder - compute from test_results when implemented
];

// Nguồn doanh thu hợp nhất: đăng ký khóa học (theo giá khóa học) + giao dịch gói dịch vụ đã hoàn thành
$revenueSourceSql = "SELECT ce.enrolled_at AS created_at, ce.id AS ref_id, ce.user_id, ce.status,
                            COALESCE(c.price, 0) AS amount, c.title AS item_title, 'course' AS source
                     FROM course_enrollments ce
                     LEFT JOIN courses c ON c.id = ce.course_id
                     WHERE ce.status IN ('active', 'completed')
                     UNION ALL
                     SELECT t.created_at, t.id AS ref_id, t.user_id, t.status,
                            t.amount, sp.name AS item_title, 'package' AS source
                     FROM transactions t
                     LEFT JOIN service_packages sp ON sp.id = t.package_id
                     WHERE t.status = 'completed'";

// Tổng doanh thu cho dashboard KPI
$db->execute("SELECT COALESCE(SUM(rev.amount), 0) AS total FROM ({$revenueSourceSql}) rev");
$revTmp = $db->getData();
$adminStats['total_revenue'] = (float)($revTmp['total'] ?? 0);

switch($action) {

    // Dashboard
    case 'index':
    default: {
        require_once 'View/admin/dashboard.php';
        break;
    }

    // Quản lý user
    case 'users': {
        // Fetch users with role info
        $sql = "SELECT u.id, u.email, u.full_name AS name, u.status, u.created_at AS joined_at, r.name AS role
                FROM users u
                LEFT JOIN user_roles ur ON ur.user_id = u.id
                LEFT JOIN roles r ON r.id = ur.role_id
                ORDER BY u.created_at DESC";
        $db->execute($sql);
        $users = [];
        while ($row = $db->getData()) {
            $row['status'] = ($row['status'] == 1) ? 'active' : 'inactive';
            $row['last_login'] = isset($row['last_login']) ? $row['last_login'] : '';
            $row['role'] = $row['role'] ?? 'user';
            $users[] = $row;
        }

        require_once 'View/admin/users.php';
        break;
    }

    // Create user (GET form / POST submit)
    case 'create_user': {
        require_once 'Model/UserModel.php';
        $roleList = (new UserModel($db))->getAllRoles();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $password = $_POST['password'] ?? '';
            $roleId = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 0;

            if ($username && $email && $password) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $userData = [
                    'username' => $username,
                    'email' => $email,
                    'password_hash' => $passwordHash,
                    'full_name' => $fullName,
                    'status' => 1,
                ];
                $userModel = new UserModel($db);
                $userModel->createUser($userData);
                $created = $userModel->getUserByEmail($email);
                if ($created && $roleId) {
                    $userModel->setRole($created['id'], $roleId);
                }
                header('Location: ?controller=admin&action=users');
                exit;
            }
        }

        require_once 'View/admin/user_form.php';
        break;
    }

    // Edit user (GET form / POST update)
    case 'edit_user': {
        $userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $userModel = new UserModel($db);
        $roleList = $userModel->getAllRoles();
        $user = $userId ? $userModel->getUserById($userId) : null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) {
            $email = trim($_POST['email'] ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $password = $_POST['password'] ?? '';
            $roleId = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 0;

            $update = ['email' => $email, 'full_name' => $fullName];
            if (!empty($password)) {
                $update['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $userModel->updateUser($userId, $update);
            if ($roleId) {
                $userModel->setRole($userId, $roleId);
            }
            header('Location: ?controller=admin&action=users');
            exit;
        }

        require_once 'View/admin/user_form.php';
        break;
    }

    // Quản lý khóa học
    case 'courses': {
        // ensure teacher_id column exists (add if missing)
        $db->execute("SELECT COUNT(*) as cnt FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'courses' AND column_name = 'teacher_id'");
        $tmpc = $db->getData();
        if (empty($tmpc['cnt'])) {
            // add teacher_id column
            $db->execute("ALTER TABLE courses ADD COLUMN teacher_id INT NULL AFTER id");
        }

        // allow filtering 'my' courses
        $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
        $userId = $_SESSION['user']['id'] ?? null;
        if ($filter === 'my' && $userId) {
            $courses = $courseModel->getAllCourses($userId);
        } else {
            $courses = $courseModel->getAllCourses();
        }

        // Fallback: if model returned unexpectedly few rows, fetch raw courses directly
        // (helps diagnose DB schema / join issues)
        if (!is_array($courses) || count($courses) <= 1) {
            $db->execute("SELECT * FROM courses ORDER BY id DESC");
            $raw = [];
            while ($r = $db->getData()) { $raw[] = $r; }
            if (count($raw) > count($courses)) {
                $courses = $raw;
            }
        }
        require_once 'View/admin/courses.php';
        break;
    }

    // Debug: list raw courses from DB (for troubleshooting missing rows)
    case 'debug_courses': {
        $db->execute("SELECT id, title, status, created_at, updated_at FROM courses ORDER BY id DESC");
        $all = [];
        while ($r = $db->getData()) { $all[] = $r; }
        echo '<h2>Raw courses (' . count($all) . ')</h2>';
        echo '<table border="1" cellpadding="6"><tr><th>id</th><th>title</th><th>status</th><th>created_at</th><th>updated_at</th></tr>';
        foreach ($all as $c) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($c['id']) . '</td>';
            echo '<td>' . htmlspecialchars($c['title']) . '</td>';
            echo '<td>' . htmlspecialchars($c['status']) . '</td>';
            echo '<td>' . htmlspecialchars($c['created_at']) . '</td>';
            echo '<td>' . htmlspecialchars($c['updated_at']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
        break;
    }

    // Assign missing teacher_id to current admin for all courses without owner
    case 'assign_missing_teachers': {
        $userId = $_SESSION['user']['id'] ?? null;
        if ($userId) {
            // set teacher_id for courses missing it
            $db->execute("UPDATE courses SET teacher_id = '{$userId}' WHERE teacher_id IS NULL OR teacher_id = 0");
        }
        header('Location: ?controller=admin&action=courses');
        exit;
        break;
    }

    // Tests management
    case 'tests': {
        $sql = "SELECT t.*, c.title AS course_title FROM tests t LEFT JOIN courses c ON c.id = t.course_id ORDER BY t.created_at DESC";
        $db->execute($sql);
        $tests = [];
        while ($r = $db->getData()) { $tests[] = $r; }
        require_once 'View/admin/tests.php';
        break;
    }

    // Create test (GET/POST)
    case 'create_test': {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'course_id' => isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0,
                'lesson_id' => isset($_POST['lesson_id']) ? (int)$_POST['lesson_id'] : null,
                'title' => trim($_POST['title'] ?? ''),
                'duration_minutes' => isset($_POST['duration_minutes']) ? (int)$_POST['duration_minutes'] : 0,
            ];
            $testModel->createTest($data);
            header('Location: ?controller=admin&action=tests');
            exit;
        }
        // fetch courses for selection
        $courses = $courseModel->getAllCourses();
        require_once 'View/admin/test_form.php';
        break;
    }

    // Chatbot configuration (manage API keys, model, prompt)
    case 'chatbot_config': {
        $db->execute("SELECT * FROM chatbot_configs ORDER BY id DESC LIMIT 1");
        $cfg = $db->getData();
        require_once 'View/admin/chatbot_config.php';
        break;
    }

    case 'chatbot_config_save': {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $api_key = trim($_POST['api_key'] ?? '');
            $model = trim($_POST['model'] ?? '');
            $system_prompt = trim($_POST['system_prompt'] ?? '');
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            // ensure api_key column exists (migration helper)
            $db->execute("SELECT column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'chatbot_configs' AND column_name = 'api_key'");
            $tmpc = $db->getData();
            if (empty($tmpc['column_name'])) {
                $db->execute("ALTER TABLE chatbot_configs ADD COLUMN api_key TEXT NULL AFTER model");
            }

            // update existing or insert
            $db->execute("SELECT id FROM chatbot_configs ORDER BY id DESC LIMIT 1");
            $existing = $db->getData();
            $safeName = $db->escapeString($name);
            $safeModel = $db->escapeString($model);
            $safePrompt = $db->escapeString($system_prompt);
            $safeKey = $db->escapeString($api_key);

            if (!empty($existing['id'])) {
                $db->execute("UPDATE chatbot_configs SET name = '{$safeName}', model = '{$safeModel}', system_prompt = '{$safePrompt}', is_active = '{$is_active}', api_key = '{$safeKey}' WHERE id = '" . (int)$existing['id'] . "'");
            } else {
                $db->execute("INSERT INTO chatbot_configs (name, system_prompt, model, is_active, api_key) VALUES ('{$safeName}', '{$safePrompt}', '{$safeModel}', '{$is_active}', '{$safeKey}')");
            }
        }
        header('Location: ?controller=admin&action=chatbot_config');
        exit;
        break;
    }

    // Edit test (GET/POST)
    case 'edit_test': {
        $testId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $test = $testId ? $testModel->getTestById($testId) : null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $testId) {
            $data = [
                'course_id' => isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0,
                'lesson_id' => isset($_POST['lesson_id']) ? (int)$_POST['lesson_id'] : null,
                'title' => trim($_POST['title'] ?? ''),
                'duration_minutes' => isset($_POST['duration_minutes']) ? (int)$_POST['duration_minutes'] : 0,
            ];
            $testModel->updateTest($testId, $data);
            header('Location: ?controller=admin&action=tests');
            exit;
        }
        $courses = $courseModel->getAllCourses();
        require_once 'View/admin/test_form.php';
        break;
    }

    // Manage questions for a test
    case 'questions': {
        $testId = isset($_GET['test_id']) ? (int)$_GET['test_id'] : 0;
        $questions = [];
        $test = null;
        if ($testId) {
            $questions = $testModel->getQuestionsByTest($testId);
            $test = $testModel->getTestById($testId);
        }
        require_once 'View/admin/questions.php';
        break;
    }

    // Create question (GET/POST)
    case 'create_question': {
        $testId = isset($_GET['test_id']) ? (int)$_GET['test_id'] : 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $qData = [
                'test_id' => isset($_POST['test_id']) ? (int)$_POST['test_id'] : 0,
                'content' => trim($_POST['content'] ?? ''),
                'type' => trim($_POST['type'] ?? 'single_choice'),
                'order_index' => isset($_POST['order_index']) ? (int)$_POST['order_index'] : 0,
            ];
            $qid = $testModel->createQuestion($qData);
            // insert answers
            $answers = $_POST['answer_content'] ?? [];
            $correct = $_POST['answer_correct'] ?? [];
            foreach ($answers as $idx => $ac) {
                $acTrim = trim($ac);
                if ($acTrim === '') continue;
                $isC = isset($correct[$idx]) ? 1 : 0;
                $testModel->createAnswer(['question_id' => $qid, 'content' => $acTrim, 'is_correct' => $isC]);
            }
            header('Location: ?controller=admin&action=questions&test_id=' . $qData['test_id']);
            exit;
        }
        $testId = $testId ?: (int)($_GET['test_id'] ?? 0);
        require_once 'View/admin/question_form.php';
        break;
    }

    // Edit question (GET/POST)
    case 'edit_question': {
        $questionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $question = $questionId ? $testModel->getQuestionById($questionId) : null;
        $answers = $questionId ? $testModel->getAnswersByQuestion($questionId) : [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $questionId) {
            $qData = [
                'content' => trim($_POST['content'] ?? ''),
                'type' => trim($_POST['type'] ?? 'single_choice'),
                'order_index' => isset($_POST['order_index']) ? (int)$_POST['order_index'] : 0,
            ];
            $testModel->updateQuestion($questionId, $qData);
            // replace answers: delete old then insert
            $db->delete('answers', "question_id = '$questionId'");
            $answersIn = $_POST['answer_content'] ?? [];
            $correct = $_POST['answer_correct'] ?? [];
            foreach ($answersIn as $idx => $ac) {
                $acTrim = trim($ac);
                if ($acTrim === '') continue;
                $isC = isset($correct[$idx]) ? 1 : 0;
                $testModel->createAnswer(['question_id' => $questionId, 'content' => $acTrim, 'is_correct' => $isC]);
            }
            $testRow = $testModel->getQuestionById($questionId);
            header('Location: ?controller=admin&action=questions&test_id=' . ($testRow['test_id'] ?? ''));
            exit;
        }
        require_once 'View/admin/question_form.php';
        break;
    }

    // Delete question
    case 'delete_question': {
        $questionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($questionId) {
            $testModel->deleteQuestion($questionId);
        }
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '?controller=admin&action=tests'));
        exit;
        break;
    }

    // View test results for a specific test
    case 'test_results': {
        $testId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $results = [];
        if ($testId) {
            $sql = "SELECT tr.*, u.full_name, u.email FROM test_results tr LEFT JOIN users u ON u.id = tr.user_id WHERE tr.test_id = '$testId' ORDER BY tr.created_at DESC";
            $db->execute($sql);
            while ($r = $db->getData()) { $results[] = $r; }
        }
        require_once 'View/admin/test_results.php';
        break;
    }

    case 'delete_test': {
        $testId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($testId) {
            // delete related questions and test
            $db->delete('questions', "test_id = '$testId'");
            $db->delete('tests', "id = '$testId'");
        }
        header('Location: ?controller=admin&action=tests');
        exit;
        break;
    }

    // Chatbot admin - sessions and messages
    case 'chatbot': {
        $sql = "SELECT cs.*, COALESCE(u.full_name, 'Khách') AS full_name, (SELECT COUNT(*) FROM chat_messages cm WHERE cm.session_id = cs.id) AS message_count FROM chat_sessions cs LEFT JOIN users u ON u.id = cs.user_id ORDER BY cs.created_at DESC";
        $db->execute($sql);
        $sessions = [];
        while ($r = $db->getData()) { $sessions[] = $r; }
        require_once 'View/admin/chatbot.php';
        break;
    }

    case 'chatbot_view': {
        $sessionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $messages = [];
        if ($sessionId) {
            $sql = "SELECT * FROM chat_messages WHERE session_id = '$sessionId' ORDER BY created_at ASC";
            $db->execute($sql);
            while ($r = $db->getData()) { $messages[] = $r; }
        }
        require_once 'View/admin/chatbot_view.php';
        break;
    }

    case 'chatbot_delete_session': {
        $sessionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($sessionId) {
            $db->delete('chat_messages', "session_id = '$sessionId'");
            $db->delete('chat_sessions', "id = '$sessionId'");
        }
        header('Location: ?controller=admin&action=chatbot');
        exit;
        break;
    }

    // Reports and statistics
    case 'reports': {
        // Basic aggregates
        $reports = [];
        $reports['users_total'] = (int)$db->countData('users');
        $reports['courses_total'] = (int)$db->countData('courses');
        $reports['tests_total'] = (int)$db->countData('tests');
        $db->execute("SELECT COUNT(*) as cnt FROM chat_sessions"); $tmp = $db->getData(); $reports['chat_sessions'] = (int)$tmp['cnt'];
        // Test pass rate (approx)
        $db->execute("SELECT AVG(score) as avg_score FROM test_results"); $tmp = $db->getData(); $reports['avg_test_score'] = round(floatval($tmp['avg_score'] ?? 0),2);
        require_once 'View/admin/reports.php';
        break;
    }

    case 'export_users': {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="users_export.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['id','username','email','full_name','status','created_at']);
        $db->execute("SELECT id, username, email, full_name, status, created_at FROM users");
        while ($r = $db->getData()) {
            fputcsv($out, [$r['id'],$r['username'],$r['email'],$r['full_name'],$r['status'],$r['created_at']]);
        }
        fclose($out);
        exit;
        break;
    }

    // Create course (GET/POST)
    case 'create_course': {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'price' => floatval(str_replace(',', '', $_POST['price'] ?? 0)),
                'level' => trim($_POST['level'] ?? ''),
                'thumbnail_url' => trim($_POST['thumbnail_url'] ?? ''),
                'promo_video_url' => trim($_POST['promo_video_url'] ?? ''),
                'status' => isset($_POST['status']) ? 1 : 0,
                'teacher_id' => isset($_POST['teacher_id']) && $_POST['teacher_id'] !== '' ? (int)$_POST['teacher_id'] : ($_SESSION['user']['id'] ?? null),
            ];
            // Basic validation
            $error = null;
            if (empty($data['title'])) {
                $error = 'Title is required';
            }
            if (!is_numeric($data['price'])) {
                $data['price'] = 0;
            }
            if ($data['promo_video_url'] !== '' && !preg_match('~^https://(?:www\.)?(?:youtube\.com/watch\?v=[A-Za-z0-9_-]{11}|youtu\.be/[A-Za-z0-9_-]{11}|player\.vimeo\.com/video/[0-9]+)|^https://[^\s]+\.mp4(?:\?[^\s]*)?$~i', $data['promo_video_url'])) {
                $error = 'Video cần là link YouTube, Vimeo hoặc MP4 hợp lệ (HTTPS).';
            }
            if ($error) {
                require_once 'View/admin/course_form.php';
                break;
            }
            $courseModel->createCourse($data);
            header('Location: ?controller=admin&action=courses');
            exit;
        }
        // fetch potential teachers
        require_once 'Model/UserModel.php';
        $userModel = new UserModel($db);
        $teachers = $userModel->getUsersByRoleName('teacher');
        require_once 'View/admin/course_form.php';
        break;
    }

    // Edit course (GET/POST)
    case 'edit_course': {
        $courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $course = $courseId ? $courseModel->getCourseById($courseId) : null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $courseId) {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'price' => floatval(str_replace(',', '', $_POST['price'] ?? 0)),
                'level' => trim($_POST['level'] ?? ''),
                'thumbnail_url' => trim($_POST['thumbnail_url'] ?? ''),
                'promo_video_url' => trim($_POST['promo_video_url'] ?? ''),
                'status' => isset($_POST['status']) ? 1 : 0,
            ];
            // do not overwrite teacher_id unless explicitly provided
            if (isset($_POST['teacher_id']) && $_POST['teacher_id'] !== '') {
                $data['teacher_id'] = (int)$_POST['teacher_id'];
            }
            // Basic validation
            $error = null;
            if (empty($data['title'])) {
                $error = 'Title is required';
            }
            if ($data['promo_video_url'] !== '' && !preg_match('~^https://(?:www\.)?(?:youtube\.com/watch\?v=[A-Za-z0-9_-]{11}|youtu\.be/[A-Za-z0-9_-]{11}|player\.vimeo\.com/video/[0-9]+)|^https://[^\s]+\.mp4(?:\?[^\s]*)?$~i', $data['promo_video_url'])) {
                $error = 'Video cần là link YouTube, Vimeo hoặc MP4 hợp lệ (HTTPS).';
            }
            if (!is_numeric($data['price'])) {
                $data['price'] = 0;
            }
            if ($error) {
                $course = $courseModel->getCourseById($courseId);
                require_once 'View/admin/course_form.php';
                break;
            }
            $courseModel->updateCourse($courseId, $data);
            header('Location: ?controller=admin&action=courses');
            exit;
        }
        require_once 'Model/UserModel.php';
        $userModel = new UserModel($db);
        $teachers = $userModel->getUsersByRoleName('teacher');
        require_once 'View/admin/course_form.php';
        break;
    }

    // Toggle course status (publish/unpublish)
    case 'toggle_course_status': {
        $courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($courseId) {
            $c = $courseModel->getCourseById($courseId);
            $newStatus = ($c && isset($c['status']) && $c['status'] == 1) ? 0 : 1;
            $courseModel->updateCourse($courseId, ['status' => $newStatus]);
        }
        header('Location: ?controller=admin&action=courses');
        exit;
        break;
    }

    // Delete course
    case 'delete_course': {
        $courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($courseId) {
            $courseModel->deleteCourse($courseId);
        }
        header('Location: ?controller=admin&action=courses');
        exit;
        break;
    }

    // Lock or unlock a user account
    case 'toggle_user': {
        // expects GET params: id and op=lock|unlock
        $userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $op = isset($_GET['op']) ? $_GET['op'] : '';
        if ($userId && in_array($op, ['lock','unlock'])) {
            $status = ($op === 'lock') ? 0 : 1;
            $db->update('users', ['status' => $status], "id = '$userId'");
        }
        header('Location: ?controller=admin&action=users');
        exit;
        break;
    }

    // Delete a user
    case 'delete_user': {
        $userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($userId) {
            $userModel->deleteUser($userId);
        }
        header('Location: ?controller=admin&action=users');
        exit;
        break;
    }

    // Quản lý doanh thu
    case 'revenue': {
        $filter = isset($_GET['filter']) ? $_GET['filter'] : '30_days';
        $allowedFilters = ['7_days', '30_days', 'this_month', 'this_year', 'all_time'];
        if (!in_array($filter, $allowedFilters)) {
            $filter = '30_days';
        }

        // KPI: tổng doanh thu + tổng số giao dịch
        $db->execute("SELECT COALESCE(SUM(rev.amount), 0) AS total, COUNT(*) AS cnt FROM ({$revenueSourceSql}) rev");
        $tmp = $db->getData();
        $totalRevenue = (float)($tmp['total'] ?? 0);
        $totalTransactions = (int)($tmp['cnt'] ?? 0);

        // KPI: doanh thu tháng này
        $db->execute("SELECT COALESCE(SUM(rev.amount), 0) AS total FROM ({$revenueSourceSql}) rev
                      WHERE YEAR(rev.created_at) = YEAR(CURDATE()) AND MONTH(rev.created_at) = MONTH(CURDATE())");
        $tmp = $db->getData();
        $monthRevenue = (float)($tmp['total'] ?? 0);

        // Dữ liệu biểu đồ theo bộ lọc thời gian
        $chartLabels = [];
        $chartValues = [];

        if ($filter === 'this_year' || $filter === 'all_time') {
            // Nhóm theo tháng
            $whereClause = ($filter === 'this_year') ? "WHERE YEAR(rev.created_at) = YEAR(CURDATE())" : "";
            $db->execute("SELECT DATE_FORMAT(rev.created_at, '%Y-%m') AS ym, SUM(rev.amount) AS total
                          FROM ({$revenueSourceSql}) rev {$whereClause}
                          GROUP BY ym ORDER BY ym ASC");
            $byMonth = [];
            while ($r = $db->getData()) { $byMonth[$r['ym']] = (float)$r['total']; }

            if ($filter === 'this_year') {
                $year = (int)date('Y');
                for ($m = 1; $m <= 12; $m++) {
                    $key = sprintf('%d-%02d', $year, $m);
                    $chartLabels[] = 'T' . $m . '/' . $year;
                    $chartValues[] = $byMonth[$key] ?? 0;
                }
            } else {
                if (empty($byMonth)) {
                    $chartLabels[] = 'T' . date('n/Y');
                    $chartValues[] = 0;
                } else {
                    foreach ($byMonth as $ym => $total) {
                        $parts = explode('-', $ym);
                        $chartLabels[] = 'T' . (int)$parts[1] . '/' . $parts[0];
                        $chartValues[] = $total;
                    }
                }
            }
        } else {
            // Nhóm theo ngày
            if ($filter === '7_days') {
                $startDate = date('Y-m-d', strtotime('-6 days'));
            } elseif ($filter === 'this_month') {
                $startDate = date('Y-m-01');
            } else { // 30_days
                $startDate = date('Y-m-d', strtotime('-29 days'));
            }

            $db->execute("SELECT DATE(rev.created_at) AS d, SUM(rev.amount) AS total
                          FROM ({$revenueSourceSql}) rev
                          WHERE DATE(rev.created_at) >= '{$startDate}'
                          GROUP BY d ORDER BY d ASC");
            $byDay = [];
            while ($r = $db->getData()) { $byDay[$r['d']] = (float)$r['total']; }

            $cursor = strtotime($startDate);
            $endTs = strtotime(date('Y-m-d'));
            while ($cursor <= $endTs) {
                $key = date('Y-m-d', $cursor);
                $chartLabels[] = date('d/m', $cursor);
                $chartValues[] = $byDay[$key] ?? 0;
                $cursor = strtotime('+1 day', $cursor);
            }
        }

        // Danh sách giao dịch chi tiết
        $db->execute("SELECT rev.*, u.full_name, u.email FROM ({$revenueSourceSql}) rev
                      LEFT JOIN users u ON u.id = rev.user_id
                      ORDER BY rev.created_at DESC");
        $transactions = [];
        while ($r = $db->getData()) { $transactions[] = $r; }

        require_once 'View/admin/revenue.php';
        break;
    }

    // Xuất báo cáo doanh thu CSV
    case 'export_revenue': {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="revenue_export.csv"');
        $out = fopen('php://output', 'w');
        // BOM để Excel đọc đúng UTF-8
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['Mã GD', 'Nguồn', 'Người mua', 'Email', 'Sản phẩm', 'Số tiền (VNĐ)', 'Trạng thái', 'Ngày giao dịch']);
        $db->execute("SELECT rev.*, u.full_name, u.email FROM ({$revenueSourceSql}) rev
                      LEFT JOIN users u ON u.id = rev.user_id
                      ORDER BY rev.created_at DESC");
        while ($r = $db->getData()) {
            fputcsv($out, [
                strtoupper(substr($r['source'], 0, 2)) . '-' . $r['ref_id'],
                $r['source'] === 'course' ? 'Khóa học' : 'Gói dịch vụ',
                $r['full_name'] ?? 'N/A',
                $r['email'] ?? '',
                $r['item_title'] ?? '',
                $r['amount'],
                $r['status'],
                $r['created_at'],
            ]);
        }
        fclose($out);
        exit;
        break;
    }

    // Unimplemented admin sections - redirect to dashboard for now
    case 'lessons': {
        header('Location: ?controller=admin&action=index');
        exit;
        break;
    }
}

?>
