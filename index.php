<?php

session_start();

// Load environment variables from .env (if present)
require_once __DIR__ . '/bootstrap/load_env.php';

// Include các file cấu hình
require_once 'Model/DBConfig.php';
require_once 'Model/UserModel.php';
require_once 'Core/Auth.php';

// Kết nối database
$db = new Database;
$db->connect();

// Lấy controller từ URL
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Routing
switch($controller) {

    // Auth Controller - Đăng nhập, Đăng ký
    case 'auth': {
        require_once 'Controller/auth/index.php';
        break;
    }

    // Course Controller - Danh sách khóa học
    case 'course': {
        require_once 'Controller/course/index.php';
        break;
    }

    // Lesson Controller - Bài học
    case 'lesson': {
        Auth::requireLogin();
        require_once 'Controller/lesson/index.php';
        break;
    }

    // Test Controller - Bài kiểm tra
    case 'test': {
        Auth::requireLogin();
        require_once 'Controller/test/index.php';
        break;
    }

    // Progress Controller - Quản lý học tập
    case 'progress': {
        Auth::requireLogin();
        require_once 'Controller/progress/index.php';
        break;
    }

    // Admin Controller - Quản lý
    case 'admin': {
        Auth::requireAdmin();
        require_once 'Controller/admin/index.php';
        break;
    }

    // Chat Controller - Chat với AI
    case 'chat': {
        require_once 'Controller/chat/index.php';
        break;
    }

    // Profile Controller - Hồ sơ người dùng
    case 'profile': {
        Auth::requireLogin();
        require_once 'Model/ProfileController.php';
        $profileController = new ProfileController();
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        if ($action === 'update') {
            $profileController->updateProfile();
        } else {
            $profileController->showProfile();
        }
        break;
    }

    case 'setting': {
        Auth::requireLogin();
        require_once 'Model/ProfileController.php';
        $profileController = new ProfileController();
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        if ($action === 'update') {
            $profileController->updateProfile();
        } elseif ($action === 'password') {
            $profileController->updatePassword();
        } else {
            $profileController->showProfile();
        }
        break;
    }

    // Home Controller - Trang chủ
    default: {
        echo "Chào mừng đến ENGLISHAI - Nền tảng học tiếng Anh tích hợp AI!";
        break;
    }
}

?>
