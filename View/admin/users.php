<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<div class="dashboard-layout">
    <div class="dashboard-container admin-page">
        <section class="admin-header">
            <div>
                <h1>Quản lý người dùng</h1>
                <p>Thêm, cập nhật trạng thái và theo dõi hoạt động người dùng.</p>
            </div>
            <div class="admin-header-actions">
                <a href="?controller=admin&action=index" class="btn btn-secondary">Dashboard</a>
                <button type="button" class="btn btn-primary">+ Thêm người dùng</button>
            </div>
        </section>

        <section class="admin-toolbar">
            <div class="admin-toolbar-title">
                <strong>Danh sách người dùng</strong>
                <span><?php echo count($users); ?> bản ghi</span>
            </div>
            <div class="admin-toolbar-actions">
                <input type="text" placeholder="Tìm theo tên hoặc email..." aria-label="Tìm kiếm người dùng">
                <select aria-label="filter role">
                    <option>Tất cả vai trò</option>
                    <option>admin</option>
                    <option>teacher</option>
                    <option>student</option>
                </select>
                <select aria-label="filter status">
                    <option>Tất cả trạng thái</option>
                    <option>active</option>
                    <option>inactive</option>
                    <option>suspended</option>
                </select>
            </div>
        </section>

        <section class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Trạng thái</th>
                        <th>Ngày tham gia</th>
                        <th>Lần đăng nhập cuối</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>#<?php echo (int) $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><span class="tag role-<?php echo htmlspecialchars($user['role']); ?>"><?php echo htmlspecialchars($user['role']); ?></span></td>
                            <td><span class="tag status-<?php echo htmlspecialchars($user['status']); ?>"><?php echo htmlspecialchars($user['status']); ?></span></td>
                            <td><?php echo htmlspecialchars($user['joined_at']); ?></td>
                            <td><?php echo htmlspecialchars($user['last_login']); ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="?controller=admin&action=edit_user&id=<?php echo (int)$user['id']; ?>" class="btn-link">Sửa</a>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <a href="?controller=admin&action=toggle_user&id=<?php echo (int)$user['id']; ?>&op=lock" class="btn-link danger">Khóa</a>
                                    <?php else: ?>
                                        <a href="?controller=admin&action=toggle_user&id=<?php echo (int)$user['id']; ?>&op=unlock" class="btn-link">Mở khóa</a>
                                    <?php endif; ?>
                                    <a href="?controller=admin&action=delete_user&id=<?php echo (int)$user['id']; ?>" class="btn-link danger" onclick="return confirm('Xóa người dùng này?');">Xóa</a>
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
