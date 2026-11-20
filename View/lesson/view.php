<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<div class="dashboard-layout">
    <div class="dashboard-container lesson-view-page">
        <section class="lesson-view-topbar">
            <a href="?controller=lesson&action=index&course_id=<?php echo (int)($lesson['course_id'] ?? 1); ?>" class="back-link">← Quay lại lộ trình</a>
            <div class="lesson-view-status">Bài <?php echo (int)($lesson['order_index'] ?? 1); ?></div>
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
                    <?php
                    $videoUrl = trim($lesson['video_url'] ?? '');
                    $videoEmbedUrl = '';
                    if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $videoUrl, $videoMatch)) {
                        $videoEmbedUrl = 'https://www.youtube-nocookie.com/embed/' . $videoMatch[1];
                    } elseif (preg_match('~^https://player\.vimeo\.com/video/([0-9]+)~', $videoUrl, $videoMatch)) {
                        $videoEmbedUrl = 'https://player.vimeo.com/video/' . $videoMatch[1];
                    }
                    ?>
                    <?php if ($videoEmbedUrl !== ''): ?>
                        <iframe class="lesson-video-frame" src="<?php echo htmlspecialchars($videoEmbedUrl, ENT_QUOTES, 'UTF-8'); ?>" title="<?php echo htmlspecialchars($lesson['title'] ?? 'Video bài học', ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    <?php elseif ($videoUrl !== '' && preg_match('~^https://[^\s]+\.mp4(?:\?[^\s]*)?$~i', $videoUrl)): ?>
                        <video class="lesson-video-frame" controls preload="metadata"><source src="<?php echo htmlspecialchars($videoUrl, ENT_QUOTES, 'UTF-8'); ?>" type="video/mp4">Trình duyệt của bạn không hỗ trợ phát video.</video>
                    <?php else: ?>
                        <div class="lesson-player-placeholder"><span>▶</span><p>Chưa có video cho bài học này.</p></div>
                    <?php endif; ?>
                </div>

                <div class="lesson-study-section">
                    <h2>Điểm chính cần nhớ</h2>
                    <?php if (!empty($lesson['lesson_content'])): ?>
                        <p><?php echo nl2br(htmlspecialchars($lesson['lesson_content'], ENT_QUOTES, 'UTF-8')); ?></p>
                    <?php endif; ?>
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
                    <?php
                        $currentIndex = null;
                        foreach ($lessons as $lessonIndex => $item) if (!empty($item['id']) && (int)$item['id'] === (int)$lesson['id']) { $currentIndex = $lessonIndex; break; }
                        $nextLesson = ($currentIndex !== null && isset($lessons[$currentIndex + 1])) ? $lessons[$currentIndex + 1] : null;
                    ?>
                    <a href="?controller=course&action=detail&course=<?php echo urlencode(get_course_slug_for_lesson($lesson['course_id'] ?? 0)); ?>" class="btn btn-primary">Thông tin khóa học</a>
                    <?php if ($nextLesson): ?>
                        <a href="?controller=lesson&action=view&id=<?php echo (int)$nextLesson['id']; ?>" class="btn btn-secondary">Bài tiếp theo</a>
                    <?php else: ?>
                        <a href="?controller=lesson&action=index&course_id=<?php echo (int)($lesson['course_id'] ?? 1); ?>" class="btn btn-secondary">Về lộ trình</a>
                    <?php endif; ?>
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
