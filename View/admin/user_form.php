<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<div class="dashboard-layout">
    <div class="dashboard-container admin-page">
        <section class="admin-header">
            <div>
                <h1><?php echo isset($user) ? 'Sửa người dùng' : 'Tạo người dùng mới'; ?></h1>
                <p>Điền thông tin và gán vai trò cho người dùng.</p>
            </div>
            <div class="admin-header-actions">
                <a href="?controller=admin&action=users" class="btn btn-secondary">Quay lại danh sách</a>
            </div>
        </section>

        <section class="admin-form">
            <form method="POST">
                <div class="form-row">
                    <label>Username</label>
                    <input name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" <?php echo isset($user) ? 'readonly' : ''; ?> required>
                </div>

                <div class="form-row">
                    <label>Email</label>
                    <input name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>

                <div class="form-row">
                    <label>Full name</label>
                    <input name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                </div>

                <div class="form-row">
                    <label>Password <?php echo isset($user) ? '(để trống giữ nguyên)' : ''; ?></label>
                    <input type="password" name="password">
                </div>

                <div class="form-row">
                    <label>Role</label>
                    <select name="role_id">
                        <option value="">Chọn role</option>
                        <?php foreach ($roleList as $r): ?>
                            <option value="<?php echo (int)$r['id']; ?>" <?php echo (isset($user) && ($user['role'] == $r['name'])) ? 'selected' : ''; ?>><?php echo htmlspecialchars($r['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary" type="submit"><?php echo isset($user) ? 'Cập nhật' : 'Tạo'; ?></button>
                    <a href="?controller=admin&action=users" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </section>
    </div>
</div>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
