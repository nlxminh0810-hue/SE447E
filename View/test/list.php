<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<div class="dashboard-layout">
    <div class="dashboard-container test-list-page">
        <section class="test-header">
            <div class="test-header-content">
                <h1>✏️ Bài Kiểm Tra</h1>
                <p>Chọn một bài kiểm tra để bắt đầu. Hãy cố gắng đạt điểm tối đa!</p>
            </div>
        </section>

        <section class="test-grid">
            <?php if (!empty($tests)): ?>
                <?php foreach ($tests as $quiz): ?>
                    <article class="test-card">
                        <div class="test-card-icon">✏️</div>
                        <div class="test-card-body">
                            <h3 class="test-card-title"><?php echo htmlspecialchars($quiz['title']); ?></h3>
                            <p class="test-card-description"><?php echo htmlspecialchars($quiz['course_title'] ?? 'Bài kiểm tra'); ?></p>

                            <div class="test-card-meta">
                                <div class="test-meta-item">
                                    <span class="meta-label">Thời gian</span>
                                    <strong><?php echo htmlspecialchars($quiz['duration']); ?></strong>
                                </div>
                                <div class="test-meta-item">
                                    <span class="meta-label">Số câu hỏi</span>
                                    <strong><?php echo (int)$quiz['totalQuestions']; ?> câu</strong>
                                </div>
                                <div class="test-meta-item">
                                    <span class="meta-label">Điểm đạt</span>
                                    <strong><?php echo (int)$quiz['passingScore']; ?>%</strong>
                                </div>
                            </div>

                            <a href="?controller=test&action=quiz&quiz=<?php echo (int)$quiz['id']; ?>" class="btn btn-primary">
                                Làm bài kiểm tra
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <?php
                    $allQuizzes = [
                        'lesson-1' => [
                            'id' => 'lesson-1',
                            'title' => 'Welcome & Course Overview - Quiz',
                            'description' => 'Kiểm tra kiến thức về lộ trình học tập và cách dùng nền tảng',
                            'duration' => '15 phút',
                            'totalQuestions' => 20,
                            'passingScore' => 70,
                        ],
                        'lesson-2' => [
                            'id' => 'lesson-2',
                            'title' => 'Vocabulary Starter Pack - Quiz',
                            'description' => 'Kiểm tra kiến thức từ vựng cơ bản',
                            'duration' => '10 phút',
                            'totalQuestions' => 15,
                            'passingScore' => 70,
                        ],
                    ];

                    foreach ($allQuizzes as $quiz): 
                ?>
                <article class="test-card">
                    <div class="test-card-icon">✏️</div>
                    <div class="test-card-body">
                        <h3 class="test-card-title"><?php echo htmlspecialchars($quiz['title']); ?></h3>
                        <p class="test-card-description"><?php echo htmlspecialchars($quiz['description']); ?></p>
                        
                        <div class="test-card-meta">
                            <div class="test-meta-item">
                                <span class="meta-label">Thời gian</span>
                                <strong><?php echo htmlspecialchars($quiz['duration']); ?></strong>
                            </div>
                            <div class="test-meta-item">
                                <span class="meta-label">Số câu hỏi</span>
                                <strong><?php echo (int)$quiz['totalQuestions']; ?> câu</strong>
                            </div>
                            <div class="test-meta-item">
                                <span class="meta-label">Điểm đạt</span>
                                <strong><?php echo (int)$quiz['passingScore']; ?>%</strong>
                            </div>
                        </div>

                        <a href="?controller=test&action=quiz&quiz=<?php echo urlencode($quiz['id']); ?>" class="btn btn-primary">
                            Làm bài kiểm tra
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section class="test-info">
            <h2>📚 Hướng dẫn làm bài kiểm tra</h2>
            <ul class="info-list">
                <li>Mỗi câu hỏi có 4 đáp án lựa chọn (A, B, C, D)</li>
                <li>Bạn chỉ có thể chọn một đáp án cho mỗi câu</li>
                <li>Sau khi gửi bài, bạn sẽ nhận được kết quả chi tiết</li>
                <li>Bạn có thể làm lại bài kiểm tra để cải thiện điểm số</li>
                <li>Điểm đạt là 70% trở lên</li>
            </ul>
        </section>
    </div>
</div>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
