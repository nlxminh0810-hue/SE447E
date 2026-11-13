<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<div class="dashboard-layout">
    <div class="dashboard-container lesson-view-page">
        <section class="lesson-view-topbar">
            <a href="?controller=lesson&action=index&course_id=<?php echo (int)($lesson['course_id'] ?? 1); ?>" class="back-link">← Quay lại lộ trình</a>
            <div class="lesson-view-status">Bài <?php echo (int)($lesson['id'] ?? 1); ?></div>
        </section>

        <section class="lesson-study-grid">
            <article class="lesson-study-main">
                <div class="lesson-study-header">
                    <div>
                        <div class="course-tag"><?php echo htmlspecialchars($lesson['type'] ?? 'Lesson'); ?></div>
                        <h1><?php echo htmlspecialchars($lesson['title'] ?? 'Bài học'); ?></h1>
                        <p><?php echo htmlspecialchars($lesson['description'] ?? ''); ?></p>
                    </div>
                    <div class="lesson-study-meta">
                        <div>
                            <span>Thời lượng</span>
                            <strong><?php echo htmlspecialchars($lesson['duration'] ?? ''); ?></strong>
                        </div>
                        <div>
                            <span>Tiến độ</span>
                            <strong><?php echo (int)($lesson['progress'] ?? 0); ?>%</strong>
                        </div>
                    </div>
                </div>

                <div class="lesson-player-card">
                    <div class="lesson-player-placeholder">
                        <span>▶</span>
                        <p>Video / Audio player</p>
                    </div>
                </div>

                <div class="lesson-study-section">
                    <h2>Điểm chính cần nhớ</h2>
                    <ul>
                        <?php foreach (($lesson['topics'] ?? []) as $topic): ?>
                            <li><?php echo htmlspecialchars($topic); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="lesson-notes-box">
                    <h2>Ghi chú nhanh</h2>
                    <p><?php echo htmlspecialchars($lesson['notes'] ?? ''); ?></p>
                </div>

                <div class="lesson-study-actions">
                    <a href="?controller=course&action=checkout&course=toeic" class="btn btn-primary">Thanh toán khoá học</a>
                    <a href="?controller=lesson&action=index&course_id=<?php echo (int)($lesson['course_id'] ?? 1); ?>" class="btn btn-secondary">Bài tiếp theo</a>
                </div>
            </article>

            <aside class="lesson-study-sidebar">
                <div class="lesson-progress-card">
                    <h3>Tiến trình</h3>
                    <div class="progress-track">
                        <div class="progress-fill" style="width: <?php echo (int)($lesson['progress'] ?? 0); ?>%;"></div>
                    </div>
                    <p><?php echo (int)($lesson['progress'] ?? 0); ?>% hoàn thành</p>
                </div>

                <div class="lesson-sidebar-card">
                    <h3>Nội dung bài học</h3>
                    <ol>
                        <?php foreach (($lesson['topics'] ?? []) as $topic): ?>
                            <li><?php echo htmlspecialchars($topic); ?></li>
                        <?php endforeach; ?>
                    </ol>
                </div>

                <div class="lesson-sidebar-card lesson-sidebar-note">
                    <h3>Gợi ý học tập</h3>
                    <p>Học lại từng phần ngắn, ghi chú từ mới và làm quiz sau mỗi bài để nhớ lâu hơn.</p>
                </div>
            </aside>
        </section>
    </div>
</div>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
