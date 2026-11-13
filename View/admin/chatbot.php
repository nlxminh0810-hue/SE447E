<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>
<div class="dashboard-layout">
    <div class="dashboard-container admin-page">
        <section class="admin-header">
            <div>
                <h1>Quản lý Chatbot</h1>
                <p>Xem lịch sử phiên chat và thống kê cơ bản.</p>
            </div>
        </section>
        <section class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>ID</th><th>User</th><th>Title</th><th>Messages</th><th>Created</th><th>Hành động</th></tr></thead>
                <tbody>
                <?php foreach ($sessions as $s): ?>
                    <tr>
                        <td><?php echo (int)$s['id']; ?></td>
                        <td><?php echo htmlspecialchars($s['full_name'] ?? 'Khách'); ?></td>
                        <td>
                            <?php echo htmlspecialchars($s['title']); ?>
                            <div style="font-size:12px;color:#888"><?php echo htmlspecialchars($s['session_key'] ?? ''); ?></div>
                        </td>
                        <td><?php echo (int)$s['message_count']; ?></td>
                        <td><?php echo htmlspecialchars($s['created_at']); ?></td>
                        <td>
                            <a href="?controller=admin&action=chatbot_view&id=<?php echo (int)$s['id']; ?>">Xem</a>
                            | <a href="?controller=admin&action=chatbot_delete_session&id=<?php echo (int)$s['id']; ?>" onclick="return confirm('Xóa phiên chat này?');">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</div>
<?php require_once 'View/layout/footer-dashboard.php'; ?>
