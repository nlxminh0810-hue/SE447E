<?php
require_once __DIR__ . '/../Model/DBConfig.php';
require_once __DIR__ . '/../Model/UserModel.php';
require_once __DIR__ . '/../Model/UserProfileModel.php';
require_once __DIR__ . '/../Core/Auth.php';

class ProfileController {
    private $userModel;
    private $userProfileModel;

    public function __construct() {
        $db = new Database();
        $db->connect();
        $this->userModel = new UserModel($db);
        $this->userProfileModel = new UserProfileModel($db);
    }

    private function currentUserId() {
        return (int)($_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? 0);
    }

    public function showProfile() {
        Auth::requireLogin();
        $userId = $this->currentUserId();
        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            header('Location: ?controller=auth&action=logout');
            exit();
        }

        $profile = $this->userProfileModel->getProfileByUserId($userId);
        $data = array_replace($user, $profile);
        $notice = $_SESSION['account_notice'] ?? null;
        unset($_SESSION['account_notice']);
        require __DIR__ . '/../View/profile/index.php';
    }

    public function updateProfile() {
        Auth::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=setting');
            exit();
        }

        $userId = $this->currentUserId();
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone_number'] ?? '');
        $avatar = trim($_POST['avatar_url'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $error = '';

        if ($fullName === '' || strlen($fullName) > 150) {
            $error = 'Vui lòng nhập họ tên (tối đa 150 ký tự).';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
            $error = 'Địa chỉ email không hợp lệ.';
        } elseif (strlen($phone) > 20) {
            $error = 'Số điện thoại không được vượt quá 20 ký tự.';
        } elseif ($avatar !== '' && (!filter_var($avatar, FILTER_VALIDATE_URL) || !in_array(strtolower((string)parse_url($avatar, PHP_URL_SCHEME)), ['http', 'https'], true))) {
            $error = 'Đường dẫn ảnh đại diện phải là URL http hoặc https hợp lệ.';
        } elseif (strlen($bio) > 2000 || strlen($address) > 500) {
            $error = 'Giới thiệu tối đa 2000 ký tự và địa chỉ tối đa 500 ký tự.';
        }

        $currentUser = $this->userModel->getUserById($userId);
        if (!$error && strtolower($email) !== strtolower($currentUser['email'] ?? '') && $this->userModel->emailExists($email)) {
            $error = 'Email này đã được sử dụng bởi tài khoản khác.';
        }

        if (!$error) {
            $savedUser = $this->userModel->updateUser($userId, [
                'full_name' => $fullName,
                'email' => $email,
                'avatar_url' => $avatar !== '' ? $avatar : null,
            ]);
            $savedProfile = $this->userProfileModel->saveProfile($userId, [
                'phone_number' => $phone,
                'address' => $address,
                'bio' => $bio,
                'avatar_url' => $avatar,
            ]);

            if ($savedUser && $savedProfile) {
                $_SESSION['user']['full_name'] = $fullName;
                $_SESSION['user']['email'] = $email;
                $_SESSION['user']['avatar_url'] = $avatar;
                $_SESSION['account_notice'] = ['type' => 'success', 'message' => 'Thông tin cá nhân đã được cập nhật.'];
            } else {
                $_SESSION['account_notice'] = ['type' => 'error', 'message' => 'Không thể lưu thông tin. Vui lòng thử lại.'];
            }
        } else {
            $_SESSION['account_notice'] = ['type' => 'error', 'message' => $error];
        }

        header('Location: ?controller=setting');
        exit();
    }

    public function updatePassword() {
        Auth::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=setting');
            exit();
        }

        $userId = $this->currentUserId();
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $user = $this->userModel->getUserById($userId);
        $message = null;

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            $message = ['type' => 'error', 'message' => 'Mật khẩu hiện tại không chính xác.'];
        } elseif (strlen($newPassword) < 8) {
            $message = ['type' => 'error', 'message' => 'Mật khẩu mới cần có ít nhất 8 ký tự.'];
        } elseif ($newPassword !== $confirmPassword) {
            $message = ['type' => 'error', 'message' => 'Mật khẩu xác nhận không khớp.'];
        } elseif (password_verify($newPassword, $user['password_hash'])) {
            $message = ['type' => 'error', 'message' => 'Mật khẩu mới phải khác mật khẩu hiện tại.'];
        } else {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $saved = $hash !== false && $this->userModel->updateUser($userId, ['password_hash' => $hash]);
            $message = $saved
                ? ['type' => 'success', 'message' => 'Mật khẩu đã được thay đổi thành công.']
                : ['type' => 'error', 'message' => 'Không thể cập nhật mật khẩu. Vui lòng thử lại.'];
        }

        $_SESSION['account_notice'] = $message;
        header('Location: ?controller=setting#password');
        exit();
    }
}
