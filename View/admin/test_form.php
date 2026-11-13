<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>
<div class="dashboard-layout">
    <div class="dashboard-container admin-page">
        <section class="admin-header">
            <div>
                <h1><?php echo isset($test['id']) ? 'Sửa đề thi' : 'Tạo đề thi mới'; ?></h1>
                <p>Quản lý thông tin đề thi.</p>
            </div>
            <div class="admin-header-actions">
                <a href="?controller=admin&action=tests" class="btn btn-secondary">Quay lại</a>
            </div>
        </section>

        <section class="admin-form-wrap">
            <form method="post">
                <div class="form-row">
                    <label>Tiêu đề</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($test['title'] ?? ''); ?>" required />
                </div>
                <div class="form-row">
                    <label>Khóa học</label>
                    <?php $preCourse = isset($_GET['course_id']) ? (int)$_GET['course_id'] : ($test['course_id'] ?? 0); ?>
                    <?php if ($preCourse): ?>
                        <?php $courseSelected = null; foreach ($courses as $c) { if ($c['id'] == $preCourse) { $courseSelected = $c; break; } } ?>
                        <input type="hidden" name="course_id" value="<?php echo (int)$preCourse; ?>" />
                        <div><?php echo htmlspecialchars($courseSelected['title'] ?? 'Course #' . $preCourse); ?></div>
                    <?php else: ?>
                        <select name="course_id">
                            <?php foreach ($courses as $c): ?>
                                <option value="<?php echo (int)$c['id']; ?>" <?php if (($test['course_id'] ?? 0) == $c['id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
                <div class="form-row">
                    <label>Thời lượng (phút)</label>
                    <input type="number" name="duration_minutes" value="<?php echo (int)($test['duration_minutes'] ?? 0); ?>" />
                </div>
                <div class="form-row">
                    <button class="btn btn-primary" type="submit">Lưu</button>
                </div>
            </form>
        </section>
    </div>
</div>
<?php require_once 'View/layout/footer-dashboard.php'; ?>
