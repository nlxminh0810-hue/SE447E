<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<div class="dashboard-layout">
    <div class="dashboard-container test-result-page">
        <?php
            // Use result prepared by controller (from session) if available
            $isPassed = false;
            $percentage = 0;
            if (!isset($result) || empty($result)) {
                echo '<div class="alert alert-info">Không tìm thấy kết quả bài kiểm tra gần nhất. Vui lòng hoàn thành bài kiểm tra rồi xem kết quả.</div>';
            } else {
                $isPassed = $result['score'] >= $result['passingScore'];
                $percentage = $result['totalQuestions'] ? ($result['correctAnswers'] / $result['totalQuestions']) * 100 : 0;
            }
        ?>

        <!-- Result Header -->
        <section class="result-header <?php echo $isPassed ? 'passed' : 'failed'; ?>">
            <div class="result-icon">
                <?php echo $isPassed ? '🎉' : '📚'; ?>
            </div>
            <h1 class="result-title">
                <?php echo $isPassed ? 'Chúc mừng! Bạn đã đạt điểm!' : 'Bạn chưa đạt điểm yêu cầu'; ?>
            </h1>
            <p class="result-message">
                <?php 
                    if ($isPassed) {
                        echo 'Bạn đã hoàn thành bài kiểm tra với kết quả tốt. Hãy tiếp tục học tập để cải thiện thêm!';
                    } else {
                        echo 'Bạn chưa đạt điểm yêu cầu. Hãy ôn tập lại và làm lại bài kiểm tra.';
                    }
                ?>
            </p>
        </section>

        <!-- Score Card -->
        <section class="result-summary">
            <div class="score-card">
                <div class="score-label">Điểm của bạn</div>
                <div class="score-value"><?php echo (int)$result['score']; ?></div>
                <div class="score-unit">/ 100</div>
                <div class="score-status">
                    <?php echo $isPassed ? '✅ Đạt' : '⚠️ Chưa đạt'; ?> (<?php echo (int)$result['passingScore']; ?>% là điểm đạt)
                </div>
            </div>

            <div class="result-stats">
                <div class="stat-item">
                    <div class="stat-value" style="color: #52c41a;">✓ <?php echo (int)$result['correctAnswers']; ?></div>
                    <div class="stat-label">Câu đúng</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" style="color: #faad14;">✗ <?php echo (int)$result['wrongAnswers']; ?></div>
                    <div class="stat-label">Câu sai</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" style="color: #999;">○ <?php echo (int)$result['unansweredQuestions']; ?></div>
                    <div class="stat-label">Chưa trả lời</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">⏱️ <?php echo htmlspecialchars($result['duration']); ?></div>
                    <div class="stat-label">Thời gian làm</div>
                </div>
            </div>
        </section>

        <!-- Quiz Metadata -->
            <?php if (!empty($result)): ?>
            <div class="result-metadata">
                <p><strong>Bài kiểm tra:</strong> <?php echo htmlspecialchars($result['quizTitle']); ?></p>
                <p><strong>Hoàn thành lúc:</strong> <?php echo htmlspecialchars($result['completedAt']); ?></p>
                <p><strong>Tỷ lệ:</strong> <?php echo (int)$result['correctAnswers']; ?>/<?php echo (int)$result['totalQuestions']; ?> câu (<?php echo round($percentage, 1); ?>%)</p>
            </div>
            <?php endif; ?>

        <!-- Detailed Answers Section -->
        <section class="result-details">
            <h2>💭 Chi tiết từng câu hỏi</h2>

            <?php if (!empty($detailedQuestions)): ?>
                <?php foreach ($detailedQuestions as $q): ?>
                <div class="result-item <?php echo $q['isCorrect'] ? 'correct' : 'incorrect'; ?>">
                    <div class="result-question-header">
                        <span class="result-question-num">Câu <?php echo (int)$q['id']; ?></span>
                        <span class="result-status">
                            <?php echo $q['isCorrect'] ? '✓ Đúng' : ($q['userAnswer'] ? '✗ Sai' : '○ Chưa trả lời'); ?>
                        </span>
                    </div>

                    <h4 class="result-question-text"><?php echo htmlspecialchars($q['question']); ?></h4>

                    <div class="result-answer">
                        <div class="answer-section">
                            <div class="answer-label">📝 Bạn chọn:</div>
                            <?php if ($q['userAnswer']): ?>
                                <div class="answer-value <?php echo $q['userAnswer'] === $q['correctAnswer'] ? 'correct-choice' : 'wrong-choice'; ?>">
                                    <strong><?php echo htmlspecialchars($q['userAnswer']); ?>.</strong> <?php echo htmlspecialchars($q['options'][$q['userAnswer']]); ?>
                                </div>
                            <?php else: ?>
                                <div class="answer-value no-answer">Bạn chưa chọn đáp án</div>
                            <?php endif; ?>
                        </div>

                        <div class="answer-section">
                            <div class="answer-label">✓ Đáp án đúng:</div>
                            <div class="answer-value correct-choice">
                                <strong><?php echo htmlspecialchars($q['correctAnswer']); ?>.</strong> <?php echo htmlspecialchars($q['options'][$q['correctAnswer']]); ?>
                            </div>
                        </div>
                    </div>

                    <div class="result-explanation">
                        <div class="explanation-label">💡 Giải thích:</div>
                            <p><?php echo htmlspecialchars($q['explanation'] ?? ''); ?></p>
                    </div>
                </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Không có chi tiết câu hỏi để hiển thị.</p>
                <?php endif; ?>
        </section>

        <!-- Action Buttons -->
        <div class="result-actions">
            <button onclick="location.href='?controller=test&action=quiz&quiz=<?php echo urlencode($result['quizId'] ?? 'lesson-1'); ?>'" class="btn btn-primary">
                🔄 Làm lại bài kiểm tra
            </button>
            <button onclick="location.href='?controller=test&action=list'" class="btn btn-secondary">
                📋 Quay lại danh sách bài kiểm tra
            </button>
            <button onclick="location.href='?controller=course'" class="btn btn-outline">
                🏠 Về trang chủ
            </button>
        </div>
    </div>
</div>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
