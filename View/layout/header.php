<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENGLISHAI - Học Tiếng Anh Với AI</title>
    <?php $styleVersion = file_exists('public/css/style.css') ? filemtime('public/css/style.css') : time(); ?>
    <link rel="stylesheet" href="public/css/style.css?v=<?php echo $styleVersion; ?>">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="?controller=course"><span class="logo-icon">🎓</span> ENGLISHAI</a>
            </div>
            <ul class="navbar-menu">
                <li><a href="?controller=course">Khóa Học</a></li>
                <li><a href="?controller=chat">Chat AI</a></li>
                
                <?php if (Auth::isLoggedIn()): ?>
                    <?php if (Auth::isAdmin()): ?>
                        <li><a href="?controller=admin">Quản Lý</a></li>
                    <?php endif; ?>
                    <li class="dropdown">
                        <a href="#"><?php echo htmlspecialchars(Auth::getUser()['name'] ?? Auth::getUser()['full_name'] ?? Auth::getUser()['username'] ?? 'Tài khoản'); ?></a>
                        <ul class="dropdown-menu">
                            <li><a href="?controller=profile">Hồ Sơ</a></li>
                            <li><a href="?controller=auth&action=logout">Đăng Xuất</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="?controller=auth&action=login">Đăng Nhập</a></li>
                    <li><a href="?controller=auth&action=register">Đăng Ký</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main class="main-content">
