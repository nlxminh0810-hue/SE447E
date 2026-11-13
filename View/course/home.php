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
                'TOEIC Intensive' => ['slug' => 'toeic', 'img' => 'toeic.jfif', 'tag' => 'Exam', 'level_key' => 'all', 'skill' => ''],
                'IELTS Mastery' => ['slug' => 'ielts', 'img' => 'ielts.jfif', 'tag' => 'Exam', 'level_key' => 'upper-intermediate', 'skill' => ''],
                'B1 Communicative' => ['slug' => 'b1', 'img' => 'b1.jfif', 'tag' => 'Level', 'level_key' => 'b1', 'skill' => ''],
                'B2 Fluency' => ['slug' => 'b2', 'img' => 'b2.jfif', 'tag' => 'Level', 'level_key' => 'b2', 'skill' => ''],
                'Reading Skills' => ['slug' => 'reading', 'img' => 'reading.jpg', 'tag' => 'Skill', 'level_key' => '', 'skill' => 'reading'],
                'Listening Skills' => ['slug' => 'listening', 'img' => 'listening.jpg', 'tag' => 'Skill', 'level_key' => '', 'skill' => 'listening'],
                'Writing Skills' => ['slug' => 'writing', 'img' => 'writing.jpg', 'tag' => 'Skill', 'level_key' => '', 'skill' => 'writing'],
                'Speaking Skills' => ['slug' => 'speaking', 'img' => 'speak.jpg', 'tag' => 'Skill', 'level_key' => '', 'skill' => 'speaking'],
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
                ];

                $normalized['is_enrolled'] = false;
                if ($currentUserId && !empty($normalized['id'])) {
                    $normalized['is_enrolled'] = $courseModel->isEnrolled($currentUserId, (int)$normalized['id']);
                }

                return $normalized;
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

            // If DB has no courses, fall back to the legacy catalog so the page still works.
            if (empty($filteredCourses)) {
                $legacyCourses = [
                    ['slug' => 'toeic', 'img' => 'toeic.jfif', 'title' => 'TOEIC Intensive', 'level' => 'All levels', 'level_key' => 'all', 'skill' => '', 'price' => '499,000₫', 'tag' => 'Exam'],
                    ['slug' => 'ielts', 'img' => 'ielts.jfif', 'title' => 'IELTS Mastery', 'level' => 'Upper-Intermediate', 'level_key' => 'upper-intermediate', 'skill' => '', 'price' => '899,000₫', 'tag' => 'Exam'],
                    ['slug' => 'b1', 'img' => 'b1.jfif', 'title' => 'B1 Communicative', 'level' => 'B1', 'level_key' => 'b1', 'skill' => '', 'price' => '299,000₫', 'tag' => 'Level'],
                    ['slug' => 'b2', 'img' => 'b2.jfif', 'title' => 'B2 Fluency', 'level' => 'B2', 'level_key' => 'b2', 'skill' => '', 'price' => '399,000₫', 'tag' => 'Level'],
                    ['slug' => 'reading', 'img' => 'reading.jpg', 'title' => 'Reading Skills', 'level' => 'Skill Practice', 'level_key' => '', 'skill' => 'reading', 'price' => '199,000₫', 'tag' => 'Skill'],
                    ['slug' => 'listening', 'img' => 'listening.jpg', 'title' => 'Listening Skills', 'level' => 'Skill Practice', 'level_key' => '', 'skill' => 'listening', 'price' => '199,000₫', 'tag' => 'Skill'],
                    ['slug' => 'writing', 'img' => 'writing.jpg', 'title' => 'Writing Skills', 'level' => 'Skill Practice', 'level_key' => '', 'skill' => 'writing', 'price' => '199,000₫', 'tag' => 'Skill'],
                    ['slug' => 'speaking', 'img' => 'speak.jpg', 'title' => 'Speaking Skills', 'level' => 'Skill Practice', 'level_key' => '', 'skill' => 'speaking', 'price' => '199,000₫', 'tag' => 'Skill'],
                ];

                $filteredCourses = array_values(array_filter($legacyCourses, function ($course) use ($searchTerm, $selectedLevel, $selectedSkill) {
                    if ($searchTerm !== '') {
                        $haystack = strtolower($course['title'] . ' ' . $course['level'] . ' ' . $course['tag'] . ' ' . $course['skill']);
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
                <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Tìm kiếm khoá học, ví dụ: TOEIC, IELTS, Reading...">
                <select name="level">
                    <option value="">Tất cả trình độ</option>
                    <option value="all" <?php echo $selectedLevel === 'all' ? 'selected' : ''; ?>>All levels</option>
                    <option value="upper-intermediate" <?php echo $selectedLevel === 'upper-intermediate' ? 'selected' : ''; ?>>Upper-Intermediate</option>
                    <option value="b1" <?php echo $selectedLevel === 'b1' ? 'selected' : ''; ?>>B1</option>
                    <option value="b2" <?php echo $selectedLevel === 'b2' ? 'selected' : ''; ?>>B2</option>
                </select>
                <select name="skill">
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
                                <div class="course-footer">
                                    <span class="price-badge"><?php echo $course['price']; ?></span>
                                    <?php if (!empty($course['is_enrolled'])): ?>
                                        <a href="?controller=lesson&action=index&course_id=<?php echo (int)$course['id']; ?>" class="enroll-btn">Bắt đầu học</a>
                                    <?php else: ?>
                                        <a href="?controller=course&action=detail&course=<?php echo urlencode($course['slug']); ?>" class="enroll-btn">Xem chi tiết</a>
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
