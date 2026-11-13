<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>
<div class="dashboard-layout">
    <div class="dashboard-container admin-page">
        <section class="admin-header">
            <div>
                <h1>Quản lý Đề Thi</h1>
                <p>Danh sách đề thi hiện có.</p>
            </div>
            <div class="admin-header-actions">
                <a href="?controller=admin&action=create_test" class="btn btn-primary">+ Tạo đề thi</a>
            </div>
        </section>
        <section class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>ID</th><th>Title</th><th>Course</th><th>Duration</th><th>Hành động</th></tr></thead>
                <tbody>
                <?php foreach ($tests as $t): ?>
                    <tr>
                        <td><?php echo (int)$t['id']; ?></td>
                        <td><?php echo htmlspecialchars($t['title']); ?></td>
                        <td><?php echo htmlspecialchars($t['course_title']); ?></td>
                        <td><?php echo (int)$t['duration_minutes']; ?> phút</td>
                        <td>
                            <a href="?controller=admin&action=test_results&id=<?php echo (int)$t['id']; ?>">Kết quả</a>
                            | <a href="?controller=admin&action=questions&test_id=<?php echo (int)$t['id']; ?>">Câu hỏi</a>
                            | <a href="?controller=admin&action=edit_test&id=<?php echo (int)$t['id']; ?>">Sửa</a>
                            | <a href="?controller=admin&action=delete_test&id=<?php echo (int)$t['id']; ?>" onclick="return confirm('Xóa đề thi?');">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</div>
<?php require_once 'View/layout/footer-dashboard.php'; ?>
