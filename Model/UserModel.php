<?php

// User Model - Quản lý dữ liệu người dùng

class UserModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Lấy user theo email
    public function getUserByEmail($email) {
        $sql = "SELECT u.*, r.id AS role_id, r.name AS role_name\n                FROM users u\n                LEFT JOIN user_roles ur ON ur.user_id = u.id\n                LEFT JOIN roles r ON r.id = ur.role_id\n                WHERE u.email = '$email'\n                LIMIT 1";
        $this->db->execute($sql);
        return $this->db->getData();
    }

    // Lấy user theo username
    public function getUserByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $this->db->execute($sql);
        return $this->db->getData();
    }

    // Lấy user theo username hoặc email
    public function getUserByLoginIdentifier($identifier) {
        $sql = "SELECT u.*, r.id AS role_id, r.name AS role_name
                FROM users u
                LEFT JOIN user_roles ur ON ur.user_id = u.id
                LEFT JOIN roles r ON r.id = ur.role_id
                WHERE u.username = '$identifier' OR u.email = '$identifier'
                LIMIT 1";
        $this->db->execute($sql);
        return $this->db->getData();
    }

    // Lấy user theo ID
    public function getUserById($id) {
        return $this->db->getDataWhere('users', "id = '$id'");
    }

    // Lấy tất cả users
    public function getAllUsers() {
        return $this->db->getAllData('users');
    }

    // Tạo user mới (đăng ký)
    public function createUser($data) {
        return $this->db->insert('users', $data);
    }

    // Gán role cho user
    public function assignRole($userId, $roleId) {
        return $this->db->insert('user_roles', [
            'user_id' => $userId,
            'role_id' => $roleId
        ]);
    }

    // Xóa tất cả role của user
    public function deleteRoles($userId) {
        return $this->db->delete('user_roles', "user_id = '$userId'");
    }

    // Đặt 1 role (thay thế các role hiện có)
    public function setRole($userId, $roleId) {
        $this->deleteRoles($userId);
        return $this->assignRole($userId, $roleId);
    }

    // Lấy danh sách roles từ DB
    public function getAllRoles() {
        return $this->db->getAllData('roles');
    }

    // Cập nhật thông tin user
    public function updateUser($id, $data) {
        $id = (int)$id;
        foreach ($data as $field => $value) {
            if ($value !== null) {
                $data[$field] = $this->db->escapeString((string)$value);
            }
        }
        return $this->db->update('users', $data, "id = '$id'");
    }

    // Xóa user
    public function deleteUser($id) {
        return $this->db->delete('users', "id = '$id'");
    }

    // Kiểm tra email đã tồn tại
    public function emailExists($email) {
        $result = $this->db->getDataWhere('users', "email = '$email'");
        return !empty($result);
    }

    // Kiểm tra username đã tồn tại
    public function usernameExists($username) {
        $result = $this->db->getDataWhere('users', "username = '$username'");
        return !empty($result);
    }

    // Kiểm tra mật khẩu
    public function verifyPassword($email, $password) {
        $user = $this->getUserByEmail($email);
        if ($user) {
            return password_verify($password, $user['password_hash']);
        }
        return false;
    }

    // Lấy user theo role
    public function getUsersByRole($role) {
        $sql = "SELECT u.* FROM users u\n                INNER JOIN user_roles ur ON ur.user_id = u.id\n                WHERE ur.role_id = '$role'";
        $this->db->execute($sql);
        $data = [];
        while ($row = $this->db->getData()) {
            $data[] = $row;
        }
        return $data;
    }

    // Lấy users theo tên role (ví dụ 'teacher')
    public function getUsersByRoleName($roleName) {
        $roleName = $this->db->escapeString($roleName);
        $sql = "SELECT u.* FROM users u\n                INNER JOIN user_roles ur ON ur.user_id = u.id\n                INNER JOIN roles r ON r.id = ur.role_id\n                WHERE r.name = '$roleName'";
        $this->db->execute($sql);
        $data = [];
        while ($row = $this->db->getData()) {
            $data[] = $row;
        }
        return $data;
    }

    // Lấy thông tin profile của user
    public function getProfile($userId) {
        $userId = (int)$userId;
        // Giả sử thông tin profile được lưu trong bảng 'user_profiles'
        // có quan hệ 1-1 với bảng 'users'
        return $this->db->getDataWhere('user_profiles', "user_id = '$userId'");
    }

    // Cập nhật thông tin profile của user
    public function updateProfile($userId, $data) {
        $userId = (int)$userId;
        return $this->db->update('user_profiles', $data, "user_id = '$userId'");
    }
}

?>
