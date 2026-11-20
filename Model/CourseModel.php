<?php

// Course Model - Quản lý khóa học

class CourseModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Lấy tất cả khóa học hoặc của 1 giáo viên (admin) nếu $teacherId được cung cấp
    public function getAllCourses($teacherId = null) {
        // include lesson count and enrolled students count
        $where = '';
        if ($teacherId !== null) {
            $teacherId = (int)$teacherId;
            $where = "WHERE c.teacher_id = '$teacherId'";
        }
        $sql = "SELECT c.*, u.full_name AS teacher_name,
            (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.id AND l.status = 1) AS lessons,
            (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.id AND l.status = 1 AND l.video_url <> '') AS video_lessons,
            (SELECT COUNT(*) FROM course_enrollments e WHERE e.course_id = c.id AND e.status IN ('active','completed')) AS students
            FROM courses c
            LEFT JOIN users u ON u.id = c.teacher_id
            $where ORDER BY c.updated_at DESC";
        $this->db->execute($sql);
        $rows = [];
        while ($r = $this->db->getData()) { $rows[] = $r; }
        return $rows;
    }

    // Lấy khóa học theo ID
    public function getCourseById($id) {
        $id = (int)$id;
        $sql = "SELECT c.*, u.full_name AS teacher_name FROM courses c LEFT JOIN users u ON u.id = c.teacher_id WHERE c.id = '$id' LIMIT 1";
        $this->db->execute($sql);
        return $this->db->getData();
    }

    // Tạo khóa học mới
    public function createCourse($data) {
        return $this->db->insert('courses', $data);
    }

    // Cập nhật khóa học
    public function updateCourse($id, $data) {
        return $this->db->update('courses', $data, "id = '$id'");
    }

    // Xóa khóa học
    public function deleteCourse($id) {
        return $this->db->delete('courses', "id = '$id'");
    }

    // Lấy khóa học của một giáo viên
    public function getCoursesByTeacher($teacherId) {
        $teacherId = (int)$teacherId;
        $sql = "SELECT c.*, u.full_name AS teacher_name,
            (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.id) AS lessons,
            (SELECT COUNT(*) FROM course_enrollments e WHERE e.course_id = c.id AND e.status IN ('active','completed')) AS students
            FROM courses c
            LEFT JOIN users u ON u.id = c.teacher_id
            WHERE c.teacher_id = '$teacherId'
            ORDER BY c.updated_at DESC";
        $this->db->execute($sql);
        $rows = [];
        while ($r = $this->db->getData()) { $rows[] = $r; }
        return $rows;
    }

    // Kiểm tra học viên đã đăng ký khóa học
    public function isEnrolled($userId, $courseId) {
        $userId = (int)$userId;
        $courseId = (int)$courseId;
        $result = $this->db->getDataWhere(
            'course_enrollments', 
            "user_id = '$userId' AND course_id = '$courseId' AND status IN ('active','completed')"
        );
        return !empty($result);
    }

    // Lấy các khóa học của một học viên
    public function getEnrolledCourses($userId) {
        $sql = "SELECT c.* FROM courses c
                JOIN course_enrollments e ON e.course_id = c.id
                WHERE e.user_id = '$userId' AND e.status IN ('active','completed')";

        $this->db->execute($sql);
        $rows = [];
        while ($row = $this->db->getData()) {
            $rows[] = $row;
        }
        return $rows;
    }
}

?>
