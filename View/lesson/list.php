<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<div class="dashboard-layout">
    <div class="dashboard-container lesson-list-page">
        <section class="lesson-hero-card">
            <div class="lesson-hero-copy">
                <div class="course-tag">Learning Space</div>
                <h1>Không gian học tập của bạn</h1>
                <p>
                    Theo dõi lộ trình, mở từng bài học và học theo tiến độ từng bước.
                    Đây là giao diện học tập mới để học viên có thể tập trung vào nội dung.
                </p>
                <div class="lesson-hero-actions">
                    <a href="?controller=course" class="btn btn-secondary">Quay lại khoá học</a>
                    <a href="?controller=lesson&action=view&id=<?php echo (int)($currentLesson['id'] ?? 1); ?>" class="btn btn-primary">Bắt đầu học ngay</a>
                </div>
            </div>

            <div class="lesson-hero-progress">
                <div class="lesson-progress-ring">
                    <span><?php echo (int)($currentLesson['progress'] ?? 0); ?>%</span>
                </div>
                <div class="lesson-hero-stats">
                    <div>
                        <strong><?php echo count($lessons ?? []); ?></strong>
                        <span>Bài học</span>
                    </div>
                    <div>
                        <strong><?php echo htmlspecialchars($currentLesson['duration'] ?? ''); ?></strong>
                        <span>Bài tiếp theo</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="lesson-grid-layout">
            <aside class="lesson-roadmap-card">
                <h2>Lộ trình bài học</h2>
                <div class="lesson-steps">
                    <?php foreach ($lessons as $lesson): ?>
                        <a class="lesson-step <?php echo ((int)$lesson['id'] === (int)($currentLesson['id'] ?? 0)) ? 'active' : ''; ?>" href="?controller=lesson&action=view&id=<?php echo (int)$lesson['id']; ?>">
                            <span class="step-badge"><?php echo (int)$lesson['id']; ?></span>
                            <span>
                                <strong><?php echo htmlspecialchars($lesson['title']); ?></strong>
                                <small><?php echo htmlspecialchars($lesson['duration']); ?> · <?php echo htmlspecialchars($lesson['type']); ?></small>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </aside>

            <div class="lesson-content-card">
                <div class="lesson-content-top">
                    <div>
                        <div class="course-tag">Today's Focus</div>
                        <h2><?php echo htmlspecialchars($currentLesson['title'] ?? 'Bài học'); ?></h2>
                        <p><?php echo htmlspecialchars($currentLesson['description'] ?? ''); ?></p>
                    </div>
                    <div class="lesson-badge-box">
                        <span><?php echo htmlspecialchars($currentLesson['duration'] ?? ''); ?></span>
                        <strong><?php echo htmlspecialchars($currentLesson['type'] ?? ''); ?></strong>
                    </div>
                </div>

                <div class="lesson-highlight-grid">
                    <div class="lesson-highlight-box">
                        <span>Tiến độ</span>
                        <strong><?php echo (int)($currentLesson['progress'] ?? 0); ?>%</strong>
                    </div>
                    <div class="lesson-highlight-box">
                        <span>Phần học</span>
                        <strong>4 mục</strong>
                    </div>
                    <div class="lesson-highlight-box">
                        <span>Trạng thái</span>
                        <strong>Đang học</strong>
                    </div>
                </div>

                <div class="lesson-video-card">
                    <div class="lesson-video-placeholder">
                        <span>▶</span>
                        <p>Video / Lesson Preview</p>
                    </div>
                </div>

                <div class="lesson-actions-row">
                    <a href="?controller=lesson&action=view&id=<?php echo (int)($currentLesson['id'] ?? 1); ?>" class="btn btn-primary">Mở bài học</a>
                    <a href="?controller=course&action=checkout&course=toeic" class="btn btn-secondary">Đăng ký khoá học</a>
                </div>
            </div>
        </section>
    </div>
</div>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
