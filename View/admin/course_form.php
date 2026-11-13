<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<div class="dashboard-layout">
    <div class="dashboard-container admin-page">
        <section class="admin-header">
            <div>
                <h1><?php echo isset($course) ? 'Sửa khóa học' : 'Tạo khóa học mới'; ?></h1>
                <p>Điền thông tin cơ bản cho khóa học.</p>
            </div>
            <div class="admin-header-actions">
                <a href="?controller=admin&action=courses" class="btn btn-secondary">Quay lại danh sách</a>
            </div>
        </section>

        <section class="admin-form">
            <form method="POST">
                <?php if (!empty($error)): ?>
                    <div class="form-error" style="color:red;margin-bottom:12px"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <div class="form-row">
                    <label>Tiêu đề</label>
                    <input name="title" value="<?php echo htmlspecialchars($course['title'] ?? ''); ?>" required>
                </div>
                <div class="form-row">
                    <label>Mô tả</label>
                    <textarea name="description"><?php echo htmlspecialchars($course['description'] ?? ''); ?></textarea>
                </div>
                <div class="form-row">
                    <label>Giá (VNĐ)</label>
                    <input name="price" value="<?php echo htmlspecialchars($course['price'] ?? '0'); ?>">
                </div>
                <div class="form-row">
                    <label>Level</label>
                    <input name="level" value="<?php echo htmlspecialchars($course['level'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <label>Ảnh thumbnail (đường dẫn)</label>
                    <input name="thumbnail_url" value="<?php echo htmlspecialchars($course['thumbnail_url'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <label>Owner / Teacher</label>
                    <select name="teacher_id">
                        <option value="">-- Không thay đổi --</option>
                        <?php foreach ($teachers as $t): ?>
                            <option value="<?php echo (int)$t['id']; ?>" <?php if ((isset($course['teacher_id']) && $course['teacher_id']==$t['id'])) echo 'selected'; ?>><?php echo htmlspecialchars($t['full_name'] ?? $t['username']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-row">
                    <label>Published</label>
                    <input type="checkbox" name="status" <?php echo (isset($course) && $course['status']==1) ? 'checked' : ''; ?>>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary" type="submit"><?php echo isset($course) ? 'Cập nhật' : 'Tạo'; ?></button>
                    <a href="?controller=admin&action=courses" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </section>
    </div>
</div>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
