<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<div class="dashboard-layout">
    <div class="dashboard-container admin-page">
        <section class="admin-header">
            <div>
                <h1>Quản lý khóa học</h1>
                <p>Quản lý nội dung khóa học, giá bán và trạng thái xuất bản.</p>
            </div>
            <div class="admin-header-actions">
                <a href="?controller=admin&action=index" class="btn btn-secondary">Dashboard</a>
                <a href="?controller=admin&action=create_course" class="btn btn-primary">+ Tạo khóa học</a>
                <a href="?controller=admin&action=assign_missing_teachers" class="btn btn-warning" onclick="return confirm('Gán admin hiện tại làm owner cho các khóa chưa có owner?');">Gán owner thiếu</a>
            </div>
        </section>

        <section class="admin-toolbar">
            <div class="admin-toolbar-title">
                <strong>Danh sách khóa học</strong>
                <span><?php echo count($courses); ?> khóa học</span>
            </div>
            <div class="admin-toolbar-actions">
                <a href="?controller=admin&action=courses" class="btn btn-link">All courses</a>
                <a href="?controller=admin&action=courses&filter=my" class="btn btn-link">My courses</a>
                <input type="text" placeholder="Tìm tên khóa học..." aria-label="Tìm kiếm khóa học">
            </div>
        </section>

        <section class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên khóa học</th>
                        <th>Danh muc</th>
                        <th>Level</th>
                        <th>Gia</th>
                        <th>Bài học</th>
                        <th>Học viên</th>
                        <th>Giáo viên</th>
                        <th>Trạng thái</th>
                        <th>Cập nhật</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td>#<?php echo (int) $course['id']; ?></td>
                            <td><?php echo htmlspecialchars($course['title']); ?></td>
                            <td><?php echo htmlspecialchars($course['category'] ?? 'General'); ?></td>
                            <td><?php echo htmlspecialchars($course['level']); ?></td>
                            <td><?php echo htmlspecialchars(number_format((float)$course['price'],0,',','.')); ?></td>
                            <td><?php echo (int) ($course['lessons'] ?? 0); ?></td>
                            <td><?php echo number_format((int) ($course['students'] ?? 0)); ?></td>
                            <td><?php echo htmlspecialchars($course['teacher_name'] ?? ($course['teacher_id'] ?? '')); ?></td>
                            <td><span class="tag status-<?php echo htmlspecialchars($course['status']); ?>"><?php echo ($course['status'] == 1) ? 'Published' : 'Draft'; ?></span></td>
                            <td><?php echo htmlspecialchars($course['updated_at']); ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="?controller=course&action=detail&id=<?php echo (int)$course['id']; ?>" class="btn-link">Preview</a>
                                    <a href="?controller=admin&action=edit_course&id=<?php echo (int)$course['id']; ?>" class="btn-link">Sửa</a>
                                    <a href="?controller=admin&action=toggle_course_status&id=<?php echo (int)$course['id']; ?>" class="btn-link"><?php echo (isset($course['status']) && $course['status']==1) ? 'Ẩn' : 'Hiện'; ?></a>
                                    <a href="?controller=admin&action=create_test&course_id=<?php echo (int)$course['id']; ?>" class="btn-link">Tạo đề</a>
                                    <a href="?controller=admin&action=delete_course&id=<?php echo (int)$course['id']; ?>" class="btn-link danger" onclick="return confirm('Xóa khóa học này?');">Xóa</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</div>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
