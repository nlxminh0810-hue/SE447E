<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>
<div class="dashboard-layout">
    <div class="dashboard-container admin-page">
        <section class="admin-header">
            <div>
                <h1>Quản lý câu hỏi cho: <?php echo htmlspecialchars($test['title'] ?? ''); ?></h1>
                <p>Danh sách câu hỏi của đề thi.</p>
            </div>
            <div class="admin-header-actions">
                <a href="?controller=admin&action=create_question&test_id=<?php echo (int)($test['id'] ?? $_GET['test_id']); ?>" class="btn btn-primary">+ Thêm câu hỏi</a>
                <a href="?controller=admin&action=tests" class="btn btn-secondary">Quay lại</a>
            </div>
        </section>
        <section class="admin-table-wrap">
            <?php if (empty($questions)): ?>
                <p>Chưa có câu hỏi cho đề thi này.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead><tr><th>ID</th><th>Nội dung</th><th>Số đáp án</th><th>Hành động</th></tr></thead>
                    <tbody>
                        <?php foreach ($questions as $q): ?>
                            <tr>
                                <td><?php echo (int)$q['id']; ?></td>
                                <td><?php echo htmlspecialchars(substr($q['content'],0,120)); ?></td>
                                <td><?php echo count($q['answers'] ?? []); ?></td>
                                <td>
                                    <a href="?controller=admin&action=edit_question&id=<?php echo (int)$q['id']; ?>">Sửa</a>
                                    | <a href="?controller=admin&action=delete_question&id=<?php echo (int)$q['id']; ?>" onclick="return confirm('Xóa câu hỏi này?');">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </div>
</div>
<?php require_once 'View/layout/footer-dashboard.php'; ?>
