<?php include 'View/layout/header.php'; ?>

<div class="container">
    <section class="courses-section">
        <h1>Danh Sách Khóa Học</h1>
        <?php if (!empty($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars($_SESSION['flash_type'] ?? 'info'); ?>" style="padding:12px;background:#e6ffed;border:1px solid #b7f0c9;margin-bottom:12px;border-radius:4px;color:#0a6b2e;">
                <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>
        
        <div class="filters">
            <input type="text" id="searchCourse" placeholder="Tìm kiếm khóa học...">
            <select id="listFilterLevel">
                <option value="">Tất cả cấp độ</option>
                <option value="Beginner">Cơ Bản</option>
                <option value="Intermediate">Trung Cấp</option>
                <option value="Advanced">Nâng Cao</option>
            </select>
        </div>

        <div class="courses-grid">
            <?php if (isset($courses) && !empty($courses)): ?>
                <?php
                    // Prepare courseModel to check enrollment status when needed
                    require_once 'Model/CourseModel.php';
                    $courseModel = new CourseModel($db);
                    $currentUserId = Auth::isLoggedIn() ? ($_SESSION['user']['id'] ?? null) : null;
                ?>
                <?php foreach ($courses as $course): ?>
                    <?php
                        // Normalize fields for catalog entries vs DB rows
                        $img = $course['thumbnail'] ?? $course['thumbnail_url'] ?? (isset($course['image']) ? 'public/images/' . $course['image'] : 'public/images/default-course.jpg');
                        $title = $course['title'] ?? '';
                        $category = $course['tag'] ?? $course['category'] ?? '';
                        $level = $course['level'] ?? '';
                        $description = $course['description'] ?? $course['short_description'] ?? '';
                        // Normalize price to numeric value for formatting
                        $priceRaw = $course['price'] ?? 0;
                        if (!is_numeric($priceRaw)) {
                            $priceNum = preg_replace('/[^0-9\.]/', '', $priceRaw);
                            $priceVal = $priceNum === '' ? 0 : (float) $priceNum;
                        } else {
                            $priceVal = (float) $priceRaw;
                        }
                        $detailLink = isset($course['id'])
                            ? '?controller=course&action=detail&id=' . (int)$course['id']
                            : '?controller=course&action=detail&course=' . urlencode($course['slug'] ?? 'toeic');
                    ?>
                    <div class="course-card">
                        <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($title); ?>" class="course-image">
                        <div class="course-content">
                            <h3><?php echo htmlspecialchars($title); ?></h3>
                            <p class="course-category"><?php echo htmlspecialchars($category); ?></p>
                            <p class="course-level">Cấp độ: <?php echo htmlspecialchars($level); ?></p>
                            <p class="course-description"><?php echo htmlspecialchars(mb_substr($description, 0, 100)) . '...'; ?></p>
                            <div class="course-footer">
                                <span class="course-price">
                                    <?php echo $priceVal > 0 ? number_format($priceVal) . ' VNĐ' : 'Miễn phí'; ?>
                                </span>
                                <?php
                                    $hasId = isset($course['id']) && $course['id'];
                                    $enrolled = false;
                                    if ($currentUserId && $hasId) {
                                        $enrolled = $courseModel->isEnrolled($currentUserId, (int)$course['id']);
                                    }
                                ?>
                                <?php if ($enrolled): ?>
                                    <a href="?controller=lesson&action=index&course_id=<?php echo (int)$course['id']; ?>" class="btn btn-success">Bắt đầu học</a>
                                <?php else: ?>
                                    <a href="<?php echo $detailLink; ?>" class="btn btn-primary">Xem Chi Tiết</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Không có khóa học nào.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include 'View/layout/footer.php'; ?>
