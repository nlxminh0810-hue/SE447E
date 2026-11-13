<?php

// Auth Controller - Xử lý đăng nhập, đăng ký

$userModel = new UserModel($db);
$error = '';
$success = '';

$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch($action) {

    // Trang đăng nhập
    case 'login': {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $login = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($login === '' || $password === '') {
                $error = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
            } else {
                $user = $userModel->getUserByLoginIdentifier($login);

                if ($user && password_verify($password, $user['password_hash'])) {
                    if (isset($user['status']) && (int) $user['status'] === 0) {
                        $error = 'Tài khoản của bạn đang bị khóa.';
                    } else {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user'] = [
                            'id' => $user['id'],
                            'username' => $user['username'],
                            'full_name' => $user['full_name'],
                            'name' => $user['full_name'] ?? $user['username'],
                            'email' => $user['email'],
                            'role' => $user['role_id'] ?? null,
                            'role_id' => $user['role_id'] ?? null,
                            'role_name' => $user['role_name'] ?? null,
                            'avatar_url' => $user['avatar_url'] ?? null
                        ];
                        header('Location: ?controller=course');
                        exit;
                    }
                } else {
                    $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
                }
            }
        }
        require_once 'View/auth/login.php';
        break;
    }

    // Trang đăng ký
    case 'register': {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username === '' || $email === '' || $password === '') {
                $error = 'Vui lòng nhập đầy đủ thông tin.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email không hợp lệ.';
            } elseif ($userModel->usernameExists($username)) {
                $error = 'Tên đăng nhập này đã được sử dụng.';
            } elseif ($userModel->emailExists($email)) {
                $error = 'Email này đã được sử dụng.';
            } else {
                $data = [
                    'username' => $username,
                    'full_name' => $username,
                    'email' => $email,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    'avatar_url' => null,
                    'status' => 1
                ];

                if ($userModel->createUser($data)) {
                    $newUser = $userModel->getUserByEmail($email);
                    if ($newUser) {
                        $userModel->assignRole($newUser['id'], 2);
                    }
                    $success = 'Đăng ký thành công. Vui lòng đăng nhập.';
                } else {
                    $error = 'Đăng ký thất bại. Vui lòng thử lại.';
                }
            }
        }
        require_once 'View/auth/register.php';
        break;
    }

    // Đăng xuất
    case 'logout': {
        Auth::logout();
        break;
    }

    default: {
        echo "Auth Action không tồn tại!";
    }
}

?>
