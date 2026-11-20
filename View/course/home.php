<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<div class="dashboard-layout">
    <div class="dashboard-container">
        <?php
            $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
            $selectedLevel = isset($_GET['level']) ? trim($_GET['level']) : '';
            $selectedSkill = isset($_GET['skill']) ? trim($_GET['skill']) : '';

            require_once 'Model/CourseModel.php';
            $courseModel = new CourseModel($db);
            $currentUserId = Auth::isLoggedIn() ? ($_SESSION['user']['id'] ?? null) : null;

            $courseMetaMap = [
                'TOEIC Intensive' => ['slug' => 'toeic', 'img' => 'toeic.jfif', 'tag' => 'Exam', 'level_key' => 'all', 'skill' => '', 'video_key' => 'TOEIC'],
                'IELTS Mastery' => ['slug' => 'ielts', 'img' => 'ielts.jfif', 'tag' => 'Exam', 'level_key' => 'upper-intermediate', 'skill' => '', 'video_key' => 'IELTS'],
                'B1 Communicative' => ['slug' => 'b1', 'img' => 'b1.jfif', 'tag' => 'Level', 'level_key' => 'b1', 'skill' => '', 'video_key' => 'B1'],
                'B2 Fluency' => ['slug' => 'b2', 'img' => 'b2.jfif', 'tag' => 'Level', 'level_key' => 'b2', 'skill' => '', 'video_key' => 'B2'],
                'Reading Skills' => ['slug' => 'reading', 'img' => 'reading.jpg', 'tag' => 'Skill', 'level_key' => '', 'skill' => 'reading', 'video_key' => 'READING'],
                'Listening Skills' => ['slug' => 'listening', 'img' => 'listening.jpg', 'tag' => 'Skill', 'level_key' => '', 'skill' => 'listening', 'video_key' => 'LISTENING'],
                'Writing Skills' => ['slug' => 'writing', 'img' => 'writing.jpg', 'tag' => 'Skill', 'level_key' => '', 'skill' => 'writing', 'video_key' => 'WRITING'],
                'Speaking Skills' => ['slug' => 'speaking', 'img' => 'speak.jpg', 'tag' => 'Skill', 'level_key' => '', 'skill' => 'speaking', 'video_key' => 'SPEAKING'],
            ];

            $normalizeCourse = function ($course) use ($courseMetaMap, $currentUserId, $courseModel) {
                $title = $course['title'] ?? '';
                $meta = $courseMetaMap[$title] ?? null;

                $priceRaw = $course['price'] ?? 0;
                $priceVal = is_numeric($priceRaw) ? (float)$priceRaw : (float)preg_replace('/[^0-9\.]/', '', (string)$priceRaw);

                $thumbnail = $course['thumbnail_url'] ?? '';
                if ($thumbnail && strpos($thumbnail, 'public/images/') === false) {
                    $thumbnail = 'public/images/' . ltrim($thumbnail, '/');
                }

                $normalized = [
                    'id' => $course['id'] ?? null,
                    'slug' => $meta['slug'] ?? strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-')),
                    'img' => $meta['img'] ?? ($thumbnail ? basename($thumbnail) : 'default-course.jpg'),
                    'title' => $title,
                    'level' => $course['level'] ?? '',
                    'level_key' => $meta['level_key'] ?? '',
                    'skill' => $meta['skill'] ?? '',
                    'price' => number_format($priceVal) . ' VNĐ',
                    'tag' => $meta['tag'] ?? 'Course',
                    'description' => $course['description'] ?? '',
                    'thumbnail_url' => $thumbnail,
                    'video_url' => trim((string)($course['promo_video_url'] ?? '')) ?: trim((string)(getenv('ENGLISHAI_COURSE_VIDEO_' . ($meta['video_key'] ?? strtoupper(preg_replace('/[^A-Z0-9]+/i', '_', $title)))) ?: '')),
                    'lessons_count' => (int)($course['lessons'] ?? 0),
                    'video_lessons' => (int)($course['video_lessons'] ?? 0),
                    'is_enrolled' => false,
                ];

                $normalized['is_enrolled'] = false;
                if ($currentUserId && !empty($normalized['id'])) {
                    $normalized['is_enrolled'] = $courseModel->isEnrolled($currentUserId, (int)$normalized['id']);
                }

                return $normalized;
            };

            $slugByCourseTitle = [
                'TOEIC Intensive' => 'toeic', 'IELTS Mastery' => 'ielts',
                'B1 Communicative' => 'b1', 'B2 Fluency' => 'b2',
                'Reading Skills' => 'reading', 'Listening Skills' => 'listening',
                'Writing Skills' => 'writing', 'Speaking Skills' => 'speaking',
            ];

            $courseDetailUrl = function ($course) use ($slugByCourseTitle) {
                if (!empty($course['id'])) return '?controller=course&action=detail&id=' . (int)$course['id'];
                $slug = $course['slug'] ?? ($slugByCourseTitle[$course['title'] ?? ''] ?? 'toeic');
                return '?controller=course&action=detail&course=' . urlencode($slug);
            };

            $courses = $courseModel->getAllCourses();
            $filteredCourses = [];
            foreach ($courses as $course) {
                $normalized = $normalizeCourse($course);

                if ($searchTerm !== '') {
                    $haystack = strtolower($normalized['title'] . ' ' . $normalized['level'] . ' ' . $normalized['tag'] . ' ' . $normalized['skill'] . ' ' . ($normalized['description'] ?? ''));
                    if (strpos($haystack, strtolower($searchTerm)) === false) {
                        continue;
                    }
                }

                if ($selectedLevel !== '' && $normalized['level_key'] !== $selectedLevel) {
                    continue;
                }

                if ($selectedSkill !== '' && $normalized['skill'] !== $selectedSkill) {
                    continue;
                }

                $filteredCourses[] = $normalized;
            }

            $legacyVideoKeys = ['toeic' => 'TOEIC', 'ielts' => 'IELTS', 'b1' => 'B1', 'b2' => 'B2', 'reading' => 'READING', 'listening' => 'LISTENING', 'writing' => 'WRITING', 'speaking' => 'SPEAKING'];
            foreach ($filteredCourses as &$filteredCourse) {
                $videoKey = $legacyVideoKeys[$filteredCourse['slug'] ?? ''] ?? '';
                $filteredCourse['video_url'] = trim((string)($filteredCourse['video_url'] ?? '')) ?: ($videoKey !== '' ? trim((string)(getenv('ENGLISHAI_COURSE_VIDEO_' . $videoKey) ?: '')) : '');
                $filteredCourse['video_lessons'] = (int)($filteredCourse['video_lessons'] ?? 0);
            }
            unset($filteredCourse);

            // If DB has no courses, fall back to the legacy catalog so the page still works.
            if (empty($courses)) {
                $legacyCourses = [
                    ['slug' => 'toeic', 'img' => 'toeic.jfif', 'title' => 'TOEIC Intensive', 'level' => 'All levels', 'level_key' => 'all', 'skill' => '', 'price' => '499,000₫', 'tag' => 'Exam', 'description' => 'Luyện nghe đọc và chiến thuật làm bài TOEIC.'],
                    ['slug' => 'ielts', 'img' => 'ielts.jfif', 'title' => 'IELTS Mastery', 'level' => 'Upper-Intermediate', 'level_key' => 'upper-intermediate', 'skill' => '', 'price' => '899,000₫', 'tag' => 'Exam', 'description' => 'Chiến lược IELTS theo band, Speaking và Writing.'],
                    ['slug' => 'b1', 'img' => 'b1.jfif', 'title' => 'B1 Communicative', 'level' => 'B1', 'level_key' => 'b1', 'skill' => '', 'price' => '299,000₫', 'tag' => 'Level', 'description' => 'Ngữ pháp, từ vựng và giao tiếp trình độ B1.'],
                    ['slug' => 'b2', 'img' => 'b2.jfif', 'title' => 'B2 Fluency', 'level' => 'B2', 'level_key' => 'b2', 'skill' => '', 'price' => '399,000₫', 'tag' => 'Level', 'description' => 'Luyện phản xạ và diễn đạt lưu loát trình độ B2.'],
                    ['slug' => 'reading', 'img' => 'reading.jpg', 'title' => 'Reading Skills', 'level' => 'Skill Practice', 'level_key' => '', 'skill' => 'reading', 'price' => '199,000₫', 'tag' => 'Skill', 'description' => 'Đọc hiểu, tìm ý chính và xử lý câu hỏi theo thời gian.'],
                    ['slug' => 'listening', 'img' => 'listening.jpg', 'title' => 'Listening Skills', 'level' => 'Skill Practice', 'level_key' => '', 'skill' => 'listening', 'price' => '199,000₫', 'tag' => 'Skill', 'description' => 'Bắt keyword và hiểu ý chính qua bài luyện nghe.'],
                    ['slug' => 'writing', 'img' => 'writing.jpg', 'title' => 'Writing Skills', 'level' => 'Skill Practice', 'level_key' => '', 'skill' => 'writing', 'price' => '199,000₫', 'tag' => 'Skill', 'description' => 'Viết câu và đoạn văn đúng ngữ pháp, rõ ý.'],
                    ['slug' => 'speaking', 'img' => 'speak.jpg', 'title' => 'Speaking Skills', 'level' => 'Skill Practice', 'level_key' => '', 'skill' => 'speaking', 'price' => '199,000₫', 'tag' => 'Skill', 'description' => 'Luyện phát âm, phản xạ nói và giao tiếp.'],
                ];

                $filteredCourses = array_values(array_filter($legacyCourses, function ($course) use ($searchTerm, $selectedLevel, $selectedSkill) {
                    if ($searchTerm !== '') {
                        $haystack = strtolower($course['title'] . ' ' . $course['level'] . ' ' . $course['tag'] . ' ' . $course['skill'] . ' ' . ($course['description'] ?? ''));
                        if (strpos($haystack, strtolower($searchTerm)) === false) {
                            return false;
                        }
                    }

                    if ($selectedLevel !== '' && $course['level_key'] !== $selectedLevel) {
                        return false;
                    }

                    if ($selectedSkill !== '' && $course['skill'] !== $selectedSkill) {
                        return false;
                    }

                    return true;
                }));
            }
        ?>

        <section class="welcome-section">
            <div class="welcome-content">
                <div class="welcome-text">
                    <h1 class="welcome-title">WELCOME</h1>
                    <p class="welcome-subtitle">
                        <?php if (Auth::isLoggedIn()): ?>
                            Chào mừng <strong><?php echo htmlspecialchars(Auth::getUser()['full_name'] ?? Auth::getUser()['username'] ?? 'Bạn'); ?></strong> quay lại ENGLISHAI!
                        <?php else: ?>
                            Hãy bắt đầu hành trình học tiếng Anh của bạn với AI
                        <?php endif; ?>
                    </p>
                </div>

                <div class="welcome-actions">
                    <?php if (!Auth::isLoggedIn()): ?>
                        <a href="?controller=auth&action=login" class="btn btn-primary">Đăng Nhập</a>
                        <a href="?controller=auth&action=register" class="btn btn-secondary">Đăng Kí</a>
                    <?php else: ?>
                        <a href="?controller=course&action=list" class="btn btn-primary">Xem Khóa Học</a>
                        <a href="?controller=chat" class="btn btn-secondary">Chat Với AI</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="welcome-illustration">
                <div class="illustration-placeholder illustration-hero-background" style="background-image: url('<?php echo htmlspecialchars(asset_image_url('background.jpg')); ?>');"></div>
            </div>
        </section>

        <!-- Suggested Courses -->
        <section class="suggested-courses">
            <div class="section-header">
                <h2>Khóa học gợi ý</h2>
                <p class="section-sub">Các khóa học nổi bật, phù hợp cho học viên và khách vãng lai xem trước nội dung.</p>
            </div>

            <form class="course-search" method="get" action="">
                <input type="hidden" name="controller" value="course">
                <input type="text" id="searchCourse" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Tìm kiếm khoá học, ví dụ: TOEIC, IELTS, Reading...">
                <select name="level" id="filterLevel">
                    <option value="">Tất cả trình độ</option>
                    <option value="all" <?php echo $selectedLevel === 'all' ? 'selected' : ''; ?>>All levels</option>
                    <option value="upper-intermediate" <?php echo $selectedLevel === 'upper-intermediate' ? 'selected' : ''; ?>>Upper-Intermediate</option>
                    <option value="b1" <?php echo $selectedLevel === 'b1' ? 'selected' : ''; ?>>B1</option>
                    <option value="b2" <?php echo $selectedLevel === 'b2' ? 'selected' : ''; ?>>B2</option>
                </select>
                <select name="skill" id="filterSkill">
                    <option value="">Tất cả kỹ năng</option>
                    <option value="reading" <?php echo $selectedSkill === 'reading' ? 'selected' : ''; ?>>Reading</option>
                    <option value="listening" <?php echo $selectedSkill === 'listening' ? 'selected' : ''; ?>>Listening</option>
                    <option value="writing" <?php echo $selectedSkill === 'writing' ? 'selected' : ''; ?>>Writing</option>
                    <option value="speaking" <?php echo $selectedSkill === 'speaking' ? 'selected' : ''; ?>>Speaking</option>
                </select>
                <button type="submit">Tìm kiếm</button>
                <?php if ($searchTerm !== ''): ?>
                    <a class="clear-search" href="?controller=course">Xoá lọc</a>
                <?php endif; ?>
            </form>

            <?php if (empty($filteredCourses)): ?>
                <div class="course-empty-state">Không tìm thấy khoá học phù hợp. Thử từ khoá khác nhé.</div>
            <?php else: ?>
                <div class="courses-grid">
                    <?php foreach ($filteredCourses as $course): ?>
                        <article class="course-card">
                            <div class="course-thumb">
                                <img src="<?php echo htmlspecialchars(asset_image_url($course['img'])); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>">
                            </div>
                            <div class="course-body">
                                <div class="course-tag"><?php echo htmlspecialchars($course['tag']); ?></div>
                                <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                                <div class="course-meta"><?php echo htmlspecialchars($course['level']); ?></div>
                                <?php if (!empty($course['video_url']) || !empty($course['video_lessons'])): ?>
                                    <span class="course-video-indicator">▶ Có video giới thiệu<?php echo !empty($course['video_lessons']) ? ' · ' . (int)$course['video_lessons'] . ' bài có video' : ''; ?></span>
                                <?php else: ?>
                                    <span class="course-video-indicator course-video-missing">Chưa thêm video khóa học</span>
                                <?php endif; ?>
                                <div class="course-footer">
                                    <span class="price-badge"><?php echo $course['price']; ?></span>
                                    <?php if (!empty($course['is_enrolled'])): ?>
                                        <?php if (!empty($course['id'])): ?>
                                            <a href="?controller=lesson&action=index&course_id=<?php echo (int)$course['id']; ?>" class="enroll-btn">Bắt đầu học</a>
                                        <?php else: ?>
                                            <a href="<?php echo htmlspecialchars($courseDetailUrl($course), ENT_QUOTES, 'UTF-8'); ?>" class="enroll-btn">Xem chi tiết</a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a href="<?php echo htmlspecialchars($courseDetailUrl($course), ENT_QUOTES, 'UTF-8'); ?>" class="enroll-btn">Xem chi tiết</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Dashboard stats removed as requested -->
    </div>
</div>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
