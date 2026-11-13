<?php

class Auth {

    // Kiểm tra người dùng đã đăng nhập chưa
    public static function requireLogin() {
        if (!isset($_SESSION['user'])) {
            header("location:?controller=auth&action=login");
            exit;
        }
    }

    // Kiểm tra quyền Admin
    public static function requireAdmin() {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? null) != 1) {
            die("Bạn không có quyền truy cập trang này!");
        }
    }

    // Kiểm tra quyền Học Viên
    public static function requireStudent() {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? null) != 2) {
            die("Chỉ học viên mới có thể truy cập!");
        }
    }

    // Kiểm tra đã đăng nhập hay chưa
    public static function isLoggedIn() {
        return isset($_SESSION['user']);
    }

    // Lấy thông tin user
    public static function getUser() {
        return $_SESSION['user'] ?? null;
    }

    // Lấy role của user
    public static function getRole() {
        return $_SESSION['user']['role'] ?? ($_SESSION['user']['role_id'] ?? null);
    }

    // Kiểm tra là Admin
    public static function isAdmin() {
        return isset($_SESSION['user']) && (($_SESSION['user']['role'] ?? null) == 1 || ($_SESSION['user']['role_id'] ?? null) == 1);
    }

    // Kiểm tra là Học Viên
    public static function isStudent() {
        return isset($_SESSION['user']) && (($_SESSION['user']['role'] ?? null) == 2 || ($_SESSION['user']['role_id'] ?? null) == 2);
    }

    // Đăng xuất
    public static function logout() {
        session_destroy();
        header("location:?controller=auth&action=login");
        exit;
    }
}

?>
