<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<div class="dashboard-layout">
    <div class="dashboard-container test-quiz-page">
        <!-- Quiz Header with Timer -->
        <section class="quiz-header">
            <div class="quiz-header-content">
                <div>
                    <h1><?php echo htmlspecialchars($quiz['title']); ?></h1>
                    <p><?php echo htmlspecialchars($quiz['description']); ?></p>
                </div>
            </div>
            <div class="quiz-header-timer">
                <div class="timer-display" id="quizTimer"><?php echo str_pad((string)(int)($quiz['duration_minutes'] ?? 15), 2, '0', STR_PAD_LEFT); ?>:00</div>
                <div class="timer-label">Hết giờ</div>
            </div>
        </section>

        <!-- Quiz Container with Sidebar -->
        <div class="quiz-container">
            <!-- Question Navigator Sidebar -->
            <aside class="quiz-navigator">
                <h3>Danh sách câu hỏi</h3>
                <div class="question-grid">
                    <?php $questionNumber = 1; ?>
                    <?php foreach ($questions as $qId => $question): ?>
                        <button type="button" class="question-nav-btn <?php echo ($questionNumber === 1) ? 'active' : ''; ?>" 
                                data-question="<?php echo (int)$qId; ?>"
                                onclick="scrollToQuestion(<?php echo (int)$qId; ?>)">
                            <?php echo (int)$questionNumber; ?>
                        </button>
                        <?php $questionNumber++; ?>
                    <?php endforeach; ?>
                </div>
                <div class="quiz-status">
                    <div class="status-item">
                        <span class="status-dot answered"></span>
                        <span>Đã làm</span>
                    </div>
                    <div class="status-item">
                        <span class="status-dot unanswered"></span>
                        <span>Chưa làm</span>
                    </div>
                </div>
            </aside>

            <!-- Quiz Form -->
            <form method="POST" action="?controller=test&action=submit" class="quiz-form" id="quizForm">
                <input type="hidden" name="quizId" value="<?php echo htmlspecialchars($quiz['id']); ?>">

                <?php $questionNumber = 1; ?>
                <?php foreach ($questions as $qId => $question): ?>
                    <section class="quiz-question" data-question-id="<?php echo (int)$qId; ?>">
                        <div class="question-header">
                            <span class="question-number">Câu <?php echo (int)$questionNumber; ?> / <?php echo count($questions); ?></span>
                        </div>
                        <h3 class="question-text"><?php echo htmlspecialchars($question['question']); ?></h3>
                        
                        <div class="question-options">
                            <?php foreach ($question['options'] as $optionKey => $optionText): ?>
                                <label class="option-label">
                                    <input type="radio" 
                                           name="q_<?php echo (int)$qId; ?>" 
                                           value="<?php echo htmlspecialchars($optionKey); ?>"
                                           class="question-radio"
                                           data-question="<?php echo (int)$qId; ?>">
                                    <span class="option-box">
                                        <span class="option-key"><?php echo htmlspecialchars($optionKey); ?></span>
                                        <span class="option-text"><?php echo htmlspecialchars($optionText); ?></span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </section>
                    <?php $questionNumber++; ?>
                <?php endforeach; ?>

                <!-- Submit Button -->
                <div class="quiz-actions">
                    <button type="submit" class="btn btn-success">Nộp bài kiểm tra</button>
                    <a href="?controller=test&action=list" class="btn btn-secondary">Quay lại danh sách</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Timer functionality - 15 minutes (900 seconds)
let timeRemaining = <?php echo (int)($quiz['duration_minutes'] ?? 15); ?> * 60;

function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${minutes}:${secs.toString().padStart(2, '0')}`;
}

function updateTimer() {
    const timerDisplay = document.getElementById('quizTimer');
    if (timerDisplay) {
        timerDisplay.textContent = formatTime(timeRemaining);
        timerDisplay.style.color = timeRemaining <= 300 ? '#f5222d' : '#52c41a';
    }
    
    if (timeRemaining > 0) {
        timeRemaining--;
        setTimeout(updateTimer, 1000);
    } else {
        // Auto submit when time runs out
        alert('Hết giờ! Bài kiểm tra sẽ được nộp tự động.');
        document.getElementById('quizForm').submit();
    }
}

// Update question status when answered
document.querySelectorAll('.question-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        const questionId = this.dataset.question;
        const navBtn = document.querySelector(`[data-question="${questionId}"]`);
        if (navBtn) {
            navBtn.classList.add('answered');
        }
    });
});

// Scroll to question
function scrollToQuestion(questionId) {
    const element = document.querySelector(`[data-question-id="${questionId}"]`);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Start timer on load
document.addEventListener('DOMContentLoaded', function() {
    updateTimer();
});
</script>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
