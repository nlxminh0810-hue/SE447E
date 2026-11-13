<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>
<div class="dashboard-layout">
    <div class="dashboard-container admin-page">
        <section class="admin-header">
            <div>
                <h1>Kết quả bài kiểm tra</h1>
                <p>Hiển thị kết quả cho bài kiểm tra đã chọn.</p>
            </div>
            <div class="admin-header-actions">
                <a href="?controller=admin&action=tests" class="btn btn-secondary">Quay lại</a>
            </div>
        </section>
        <section class="admin-table-wrap">
            <?php if (empty($results)): ?>
                <p>Không có kết quả cho bài kiểm tra này.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead><tr><th>ID</th><th>User</th><th>Email</th><th>Score</th><th>Created</th></tr></thead>
                    <tbody>
                        <?php foreach ($results as $r): ?>
                        <tr>
                            <td><?php echo (int)$r['id']; ?></td>
                            <td><?php echo htmlspecialchars($r['full_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($r['email'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($r['score']); ?></td>
                            <td><?php echo htmlspecialchars($r['created_at']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </div>
</div>
<?php require_once 'View/layout/footer-dashboard.php'; ?>
