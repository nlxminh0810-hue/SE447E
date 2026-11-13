<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<div class="dashboard-layout">
    <div class="dashboard-container course-detail-page">
        <section class="course-detail-hero">
            <div class="course-detail-visual">
                <img src="<?php echo htmlspecialchars(asset_image_url($course['image'])); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>">
            </div>

            <div class="course-detail-info">
                <div class="course-tag"><?php echo htmlspecialchars($course['tag']); ?></div>
                <h1><?php echo htmlspecialchars($course['title']); ?></h1>
                <p class="course-detail-description"><?php echo htmlspecialchars($course['short_description']); ?></p>

                <div class="course-detail-meta">
                    <div>
                        <span class="meta-label">Trình độ</span>
                        <strong><?php echo htmlspecialchars($course['level']); ?></strong>
                    </div>
                    <div>
                        <span class="meta-label">Thời lượng</span>
                        <strong><?php echo htmlspecialchars($course['duration']); ?></strong>
                    </div>
                    <div>
                        <span class="meta-label">Bài học</span>
                        <strong><?php echo (int) $course['lessons']; ?> lessons</strong>
                    </div>
                    <div>
                        <span class="meta-label">Học viên</span>
                        <strong><?php echo number_format((int) $course['students']); ?></strong>
                    </div>
                </div>

                <div class="course-detail-actions">
                    <div class="price-badge course-detail-price"><?php echo htmlspecialchars($course['price']); ?></div>
                    <?php 
                        $courseId = get_course_id_by_slug($course['slug']);
                    ?>
                    <?php if (!Auth::isLoggedIn()): ?>
                        <a href="?controller=auth&action=login" class="btn btn-success">Đăng nhập để học</a>
                        <a href="?controller=auth&action=login&next=<?php echo urlencode('?controller=course&action=checkout&course=' . $course['slug']); ?>" class="btn btn-primary">Thanh toán khoá học</a>
                    <?php else: ?>
                        <?php if (!empty($isEnrolled)): ?>
                            <a href="?controller=lesson&action=index&course_id=<?php echo (int)$courseId; ?>" class="btn btn-success">Bắt đầu học</a>
                        <?php else: ?>
                            <a href="?controller=course&action=checkout&course=<?php echo urlencode($course['slug']); ?>" class="btn btn-primary">Mua khoá học</a>
                            <span class="note">(Bạn cần mua khoá học để truy cập nội dung)</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    <a href="?controller=course" class="btn btn-secondary">Quay lại trang chủ</a>
                </div>
            </div>
        </section>

        <section class="course-detail-grid">
            <article class="course-detail-panel">
                <h2>Bạn sẽ nhận được gì?</h2>
                <ul class="feature-list">
                    <?php foreach ($course['benefits'] as $benefit): ?>
                        <li><?php echo htmlspecialchars($benefit); ?></li>
                    <?php endforeach; ?>
                </ul>
            </article>

            <article class="course-detail-panel">
                <h2>Lộ trình học</h2>
                <ol class="outline-list">
                    <?php foreach ($course['outline'] as $outlineItem): ?>
                        <li><?php echo htmlspecialchars($outlineItem); ?></li>
                    <?php endforeach; ?>
                </ol>
            </article>
        </section>

        <section class="course-note-card">
            <h2>Ghi chú khoá học</h2>
            <p><?php echo htmlspecialchars($course['payment_note']); ?></p>
        </section>
    </div>
</div>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
