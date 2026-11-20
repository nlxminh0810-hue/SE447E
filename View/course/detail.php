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
                        <a href="?controller=auth&action=login&next=<?php echo urlencode(!empty($course['id']) ? '?controller=course&action=detail&id=' . (int)$course['id'] : '?controller=course&action=detail&course=' . $course['slug']); ?>" class="btn btn-success">Đăng nhập để học</a>
                        <a href="?controller=auth&action=login&next=<?php echo urlencode(!empty($course['id']) ? '?controller=course&action=checkout&id=' . (int)$course['id'] : '?controller=course&action=checkout&course=' . $course['slug']); ?>" class="btn btn-primary">Thanh toán khoá học</a>
                    <?php else: ?>
                        <?php if (!empty($isEnrolled)): ?>
                            <a href="?controller=lesson&action=index&course_id=<?php echo (int)$courseId; ?>" class="btn btn-success">Bắt đầu học</a>
                        <?php else: ?>
                            <a href="<?php echo !empty($course['id']) ? '?controller=course&action=checkout&id=' . (int)$course['id'] : '?controller=course&action=checkout&course=' . urlencode($course['slug']); ?>" class="btn btn-primary">Mua khoá học</a>
                            <span class="note">(Bạn cần mua khoá học để truy cập nội dung)</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    <a href="?controller=course" class="btn btn-secondary">Quay lại trang chủ</a>
                </div>
            </div>
        </section>

        <section class="course-detail-grid">
            <?php
                $promoUrl = trim($course['promo_video_url'] ?? '');
                if ($promoUrl === '') {
                $videoKeyBySlug = ['toeic' => 'TOEIC', 'ielts' => 'IELTS', 'b1' => 'B1', 'b2' => 'B2', 'reading' => 'READING', 'listening' => 'LISTENING', 'writing' => 'WRITING', 'speaking' => 'SPEAKING'];
                $videoKey = $videoKeyBySlug[$course['slug'] ?? ''] ?? strtoupper(preg_replace('/[^A-Z0-9]+/i', '_', $course['title'] ?? ''));
                $promoUrl = getenv('ENGLISHAI_COURSE_VIDEO_' . $videoKey) ?: '';
                }
                $promoEmbed = '';
                if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $promoUrl, $promoMatch)) {
                    $promoEmbed = 'https://www.youtube-nocookie.com/embed/' . $promoMatch[1];
                } elseif (preg_match('~^https://player\.vimeo\.com/video/([0-9]+)~', $promoUrl, $promoMatch)) {
                    $promoEmbed = 'https://player.vimeo.com/video/' . $promoMatch[1];
                }
            ?>
            <?php if ($promoEmbed === '' && !preg_match('~^https://[^\s]+\.mp4(?:\?[^\s]*)?$~i', $promoUrl)): ?>
                <article class="course-detail-panel course-promo-video course-video-config-note">
                    <h2>Video khóa học</h2>
                    <p>Chưa cấu hình URL video cho khóa học này. Thêm link YouTube/Vimeo/MP4 trong phần Sửa khóa học ở trang quản trị để hiện video tại đây.</p>
                </article>
            <?php endif; ?>
            <?php if ($promoEmbed !== '' || preg_match('~^https://[^\s]+\.mp4(?:\?[^\s]*)?$~i', $promoUrl)): ?>
                <article class="course-detail-panel course-promo-video">
                    <h2>Video giới thiệu khóa học</h2>
                    <?php if ($promoEmbed !== ''): ?>
                        <iframe class="lesson-video-frame" src="<?php echo htmlspecialchars($promoEmbed, ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlspecialchars($course['title'] . ' — giới thiệu', ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" allowfullscreen></iframe>
                    <?php else: ?>
                        <video class="lesson-video-frame" controls preload="metadata"><source src="<?php echo htmlspecialchars($promoUrl, ENT_QUOTES, 'UTF-8'); ?>" type="video/mp4"></video>
                    <?php endif; ?>
                </article>
            <?php endif; ?>
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
