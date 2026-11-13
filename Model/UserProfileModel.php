<?php

// UserProfile Model - Quản lý hồ sơ người dùng

class UserProfileModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    private function ensureProfileTable() {
        $this->db->execute("CREATE TABLE IF NOT EXISTS user_profiles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL UNIQUE,
            bio TEXT NULL,
            phone_number VARCHAR(20) NULL,
            address TEXT NULL,
            avatar_url VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_user_profiles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $columns = [];
        $result = $this->db->execute('SHOW COLUMNS FROM user_profiles');
        while ($row = $this->db->getData()) {
            $columns[$row['Field']] = true;
        }

        $alterStatements = [];
        if (!isset($columns['bio'])) {
            $alterStatements[] = 'ALTER TABLE user_profiles ADD COLUMN bio TEXT NULL';
        }
        if (!isset($columns['phone_number'])) {
            $alterStatements[] = 'ALTER TABLE user_profiles ADD COLUMN phone_number VARCHAR(20) NULL';
        }
        if (!isset($columns['address'])) {
            $alterStatements[] = 'ALTER TABLE user_profiles ADD COLUMN address TEXT NULL';
        }
        if (!isset($columns['avatar_url'])) {
            $alterStatements[] = 'ALTER TABLE user_profiles ADD COLUMN avatar_url VARCHAR(255) NULL';
        }

        foreach ($alterStatements as $statement) {
            $this->db->execute($statement);
        }
    }

    private function normalizeProfileData($profile) {
        if (!is_array($profile)) {
            return [];
        }

        $allowedFields = ['id', 'user_id', 'bio', 'phone_number', 'address', 'avatar_url', 'created_at', 'updated_at'];
        $normalized = [];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $profile)) {
                $normalized[$field] = $profile[$field];
            }
        }

        return $normalized;
    }

    private function sanitizeProfileData($data) {
        $allowedFields = ['bio', 'phone_number', 'address', 'avatar_url'];
        $sanitized = [];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $value = trim((string)$data[$field]);
            $sanitized[$field] = $value === '' ? null : $value;
            }
        }

        return $sanitized;
    }

    /**
     * Lấy hồ sơ theo ID người dùng
     * @param int $userId
     * @return array
     */
    public function getProfileByUserId($userId) {
        $this->ensureProfileTable();
        $userId = (int)$userId;
        $profile = $this->db->getDataWhere('user_profiles', "user_id = '$userId'");
        return $this->normalizeProfileData($profile);
    }

    /**
     * Tạo hoặc cập nhật hồ sơ người dùng
     * @param int $userId
     * @param array $data Dữ liệu hồ sơ (ví dụ: ['avatar' => 'url', 'bio' => 'text'])
     * @return bool
     */
    public function saveProfile($userId, $data) {
        $this->ensureProfileTable();
        $userId = (int)$userId;
        $profileData = $this->sanitizeProfileData($data);
        foreach ($profileData as $field => $value) {
            if ($value !== null) {
                $profileData[$field] = $this->db->escapeString($value);
            }
        }
        $existingProfile = $this->getProfileByUserId($userId);

        if (!empty($existingProfile)) {
            return $this->db->update('user_profiles', $profileData, "user_id = '$userId'");
        }

        $profileData['user_id'] = $userId;
        return $this->db->insert('user_profiles', $profileData);
    }
}
?>
