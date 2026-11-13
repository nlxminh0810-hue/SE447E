<?php

// Lesson Model - Quản lý bài học

class LessonModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Lấy tất cả bài học
    public function getAllLessons() {
        return $this->db->getAllData('lessons');
    }

    // Lấy bài học theo ID
    public function getLessonById($id) {
        return $this->db->getDataWhere('lessons', "id = '$id'");
    }

    // Lấy bài học của một khóa học
    public function getLessonsByCourse($courseId) {
        $courseId = (int)$courseId;
        $sql = "SELECT * FROM lessons WHERE course_id = '$courseId' AND status = 1 ORDER BY order_index ASC, id ASC";
        $this->db->execute($sql);
        $rows = [];
        while ($row = $this->db->getData()) {
            $rows[] = $row;
        }
        return $rows;
    }

    // Tạo bài học mới
    public function createLesson($data) {
        return $this->db->insert('lessons', $data);
    }

    // Cập nhật bài học
    public function updateLesson($id, $data) {
        return $this->db->update('lessons', $data, "id = '$id'");
    }

    // Xóa bài học
    public function deleteLesson($id) {
        return $this->db->delete('lessons', "id = '$id'");
    }

    // Kiểm tra học viên đã hoàn thành bài học
    public function isCompleted($userId, $lessonId) {
        $result = $this->db->getDataWhere(
            'lesson_progress',
            "user_id = '$userId' AND lesson_id = '$lessonId' AND completed = 1"
        );
        return !empty($result);
    }

    // Đánh dấu bài học hoàn thành
    public function completeLesson($userId, $lessonId) {
        $data = [
            'user_id' => $userId,
            'lesson_id' => $lessonId,
            'completed' => 1,
            'completed_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert('lesson_progress', $data);
    }
}

?>
