<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<div class="dashboard-layout">
    <div class="dashboard-container progress-page">
        <section class="progress-hero">
            <div>
                <h1>Quản lý học tập</h1>
                <p>Xin chào <?php echo htmlspecialchars($displayName); ?>, theo dõi tiến độ học tập và kế hoạch tuần của bạn tại đây.</p>
            </div>
            <div class="progress-hero-actions">
                <a href="?controller=test&action=list" class="btn btn-primary">Làm bài kiểm tra</a>
                <a href="?controller=lesson&action=index&course_id=1" class="btn btn-secondary">Tiếp tục học</a>
            </div>
        </section>

        <section class="progress-overview-grid">
            <article class="progress-overview-card">
                <span>Tổng khóa học</span>
                <strong><?php echo (int) $learningOverview['totalCourses']; ?></strong>
            </article>
            <article class="progress-overview-card">
                <span>Đang học</span>
                <strong><?php echo (int) $learningOverview['activeCourses']; ?></strong>
            </article>
            <article class="progress-overview-card">
                <span>Đã hoàn thành</span>
                <strong><?php echo (int) $learningOverview['completedCourses']; ?></strong>
            </article>
            <article class="progress-overview-card">
                <span>Điểm trung bình</span>
                <strong><?php echo (int) $learningOverview['avgScore']; ?>%</strong>
            </article>
            <article class="progress-overview-card">
                <span>Study streak</span>
                <strong><?php echo (int) $learningOverview['studyStreak']; ?> ngày</strong>
            </article>
            <article class="progress-overview-card">
                <span>Giờ học</span>
                <strong><?php echo (int) $learningOverview['studyHours']; ?>h</strong>
            </article>
        </section>

        <section class="progress-main-grid">
            <div class="progress-column">
                <article class="progress-panel">
                    <div class="panel-head">
                        <h2>Khóa học đang học</h2>
                    </div>
                    <div class="active-course-list">
                        <?php foreach ($activeCourses as $course): ?>
                            <div class="active-course-item">
                                <div class="active-course-top">
                                    <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                                    <span class="score-chip"><?php echo (int) $course['score']; ?>%</span>
                                </div>
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: <?php echo (int) $course['progress']; ?>%;"></div>
                                </div>
                                <div class="active-course-meta">
                                    <span>Tiến độ: <?php echo (int) $course['progress']; ?>%</span>
                                    <span>Bài tiếp theo: <?php echo htmlspecialchars($course['nextLesson']); ?></span>
                                    <span>Hạn chót: <?php echo htmlspecialchars($course['deadline']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>

                <article class="progress-panel">
                    <div class="panel-head">
                        <h2>Kế hoạch học tập trong tuần</h2>
                    </div>
                    <ul class="weekly-plan-list">
                        <?php foreach ($weeklyPlan as $plan): ?>
                            <li class="plan-item plan-<?php echo htmlspecialchars($plan['status']); ?>">
                                <div>
                                    <strong><?php echo htmlspecialchars($plan['day']); ?></strong>
                                    <p><?php echo htmlspecialchars($plan['task']); ?></p>
                                </div>
                                <span class="plan-status">
                                    <?php
                                        if ($plan['status'] === 'done') {
                                            echo 'Đã xong';
                                        } elseif ($plan['status'] === 'today') {
                                            echo 'Hôm nay';
                                        } else {
                                            echo 'Sắp tới';
                                        }
                                    ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            </div>

            <div class="progress-column">
                <article class="progress-panel">
                    <div class="panel-head">
                        <h2>Tiến độ kỹ năng</h2>
                    </div>
                    <div class="skill-list">
                        <?php foreach ($skillProgress as $skill): ?>
                            <div class="skill-item">
                                <div class="skill-row">
                                    <span><?php echo htmlspecialchars($skill['name']); ?></span>
                                    <strong><?php echo (int) $skill['percent']; ?>%</strong>
                                </div>
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: <?php echo (int) $skill['percent']; ?>%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>

                <article class="progress-panel">
                    <div class="panel-head">
                        <h2>Bài kiểm tra gần đây</h2>
                    </div>
                    <div class="recent-test-list">
                        <?php foreach ($recentTests as $test): ?>
                            <div class="recent-test-item">
                                <div>
                                    <h3><?php echo htmlspecialchars($test['name']); ?></h3>
                                    <p><?php echo htmlspecialchars($test['date']); ?></p>
                                </div>
                                <div class="test-badge <?php echo $test['passed'] ? 'pass' : 'fail'; ?>">
                                    <?php echo (int) $test['score']; ?>%
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="?controller=test&action=list" class="link-more">Xem tất cả bài kiểm tra</a>
                </article>
            </div>
        </section>
    </div>
</div>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
