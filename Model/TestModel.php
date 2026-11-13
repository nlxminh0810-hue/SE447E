<?php

// Test Model - Quản lý bài kiểm tra

class TestModel {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Lấy tất cả bài test
    public function getAllTests() {
        $sql = "SELECT t.*, c.title AS course_title FROM tests t LEFT JOIN courses c ON c.id = t.course_id ORDER BY t.created_at DESC";
        $this->db->execute($sql);
        $rows = [];
        while ($r = $this->db->getData()) { $rows[] = $r; }
        return $rows;
    }

    // Lấy bài test theo ID
    public function getTestById($id) {
        return $this->db->getDataWhere('tests', "id = '$id'");
    }

    // Lấy bài test của một khóa học
    public function getTestsByCourse($courseId) {
        $courseId = (int)$courseId;
        $sql = "SELECT * FROM tests WHERE course_id = '$courseId' ORDER BY created_at DESC";
        $this->db->execute($sql);
        $rows = [];
        while ($r = $this->db->getData()) { $rows[] = $r; }
        return $rows;
    }

    // Lấy câu hỏi của một bài test
    public function getQuestionsByTest($testId) {
        $testId = (int)$testId;
        $sql = "SELECT * FROM questions WHERE test_id = '$testId' ORDER BY order_index ASC, id ASC";
        $this->db->execute($sql);
        $questions = [];
        $rows = [];
        while ($q = $this->db->getData()) {
            $rows[] = $q;
        }

        foreach ($rows as $q) {
            $qid = (int)$q['id'];
            $q['answers'] = $this->getAnswersByQuestion($qid);
            $questions[] = $q;
        }
        return $questions;
    }

    // Tạo bài test mới
    public function createTest($data) {
        $this->db->insert('tests', $data);
        $conn = $this->db->connect();
        return $conn->insert_id;
    }

    // Tạo câu hỏi
    public function createQuestion($data) {
        $this->db->insert('questions', $data);
        // return last insert id
        $conn = $this->db->connect();
        return $conn->insert_id;
    }

    public function createAnswer($data) {
        return $this->db->insert('answers', $data);
    }

    public function getQuestionById($id) {
        return $this->db->getDataWhere('questions', "id = '$id'");
    }

    public function getAnswersByQuestion($questionId) {
        $questionId = (int)$questionId;
        $this->db->execute("SELECT * FROM answers WHERE question_id = '$questionId'");
        $arr = [];
        while ($r = $this->db->getData()) { $arr[] = $r; }
        return $arr;
    }

    public function updateQuestion($id, $data) {
        return $this->db->update('questions', $data, "id = '$id'");
    }

    public function updateTest($id, $data) {
        return $this->db->update('tests', $data, "id = '$id'");
    }

    public function deleteQuestion($id) {
        // delete answers first
        $this->db->delete('answers', "question_id = '$id'");
        return $this->db->delete('questions', "id = '$id'");
    }

    public function deleteTest($id) {
        // cascade deletes via FK should remove questions/answers, but ensure cleanup
        $this->db->delete('answers', "question_id IN (SELECT id FROM questions WHERE test_id = '$id')");
        $this->db->delete('questions', "test_id = '$id'");
        return $this->db->delete('tests', "id = '$id'");
    }

    // Nộp bài test (lưu kết quả)
    public function submitTest($userId, $testId, $answers) {
        $data = [
            'user_id' => $userId,
            'test_id' => $testId,
            'answers' => json_encode($answers),
            'submitted_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert('test_results', $data);
    }

    // Lấy kết quả test
    public function getTestResult($id) {
        return $this->db->getDataWhere('test_results', "id = '$id'");
    }

    // Lấy lịch sử test của user
    public function getUserTestHistory($userId) {
        return $this->db->getAllData('test_results');
        // TODO: Filter by user_id
    }
}

?>
