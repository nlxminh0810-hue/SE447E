<?php require_once __DIR__ . '/../layout/header-dashboard.php'; ?>
<?php require_once __DIR__ . '/../layout/sidebar.php'; ?>

<main class="dashboard-layout">
    <div class="dashboard-container account-settings">
        <header class="settings-heading">
            <div class="settings-avatar">
                <?php if (!empty($data['avatar_url'])): ?>
                    <img src="<?php echo htmlspecialchars($data['avatar_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="Ảnh đại diện">
                <?php else: ?>
                    <span><?php echo htmlspecialchars(mb_strtoupper(mb_substr($data['full_name'] ?? $data['username'] ?? 'U', 0, 1)), ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endif; ?>
            </div>
            <div>
                <p class="settings-eyebrow">TÀI KHOẢN</p>
                <h1>Cài đặt tài khoản</h1>
                <p class="settings-subtitle">Quản lý thông tin cá nhân và bảo mật tài khoản EnglishAI.</p>
            </div>
        </header>

        <?php if (!empty($notice)): ?>
            <div class="settings-alert <?php echo $notice['type'] === 'success' ? 'is-success' : 'is-error'; ?>" role="status">
                <?php echo htmlspecialchars($notice['message'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <section class="settings-card">
            <div class="settings-card-heading">
                <div><h2>Thông tin cá nhân</h2><p>Cập nhật thông tin dùng trong tài khoản của bạn.</p></div>
            </div>
            <form action="?controller=setting&action=update" method="POST" class="settings-form">
                <div class="settings-fields">
                    <label class="settings-field">
                        <span>Họ và tên <b>*</b></span>
                        <input type="text" name="full_name" maxlength="150" required value="<?php echo htmlspecialchars($data['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" autocomplete="name">
                    </label>
                    <label class="settings-field">
                        <span>Email <b>*</b></span>
                        <input type="email" name="email" maxlength="190" required value="<?php echo htmlspecialchars($data['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" autocomplete="email">
                    </label>
                    <label class="settings-field">
                        <span>Số điện thoại</span>
                        <input type="tel" name="phone_number" maxlength="20" value="<?php echo htmlspecialchars($data['phone_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" autocomplete="tel">
                    </label>
                    <label class="settings-field">
                        <span>Ảnh đại diện (URL)</span>
                        <input type="url" name="avatar_url" placeholder="https://example.com/avatar.jpg" value="<?php echo htmlspecialchars($data['avatar_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </label>
                    <label class="settings-field settings-field-wide">
                        <span>Địa chỉ</span>
                        <input type="text" name="address" maxlength="500" value="<?php echo htmlspecialchars($data['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" autocomplete="street-address">
                    </label>
                    <label class="settings-field settings-field-wide">
                        <span>Giới thiệu bản thân</span>
                        <textarea name="bio" rows="4" maxlength="2000" placeholder="Chia sẻ đôi điều về mục tiêu học tiếng Anh của bạn..."><?php echo htmlspecialchars($data['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </label>
                </div>
                <div class="settings-actions"><button type="submit" class="settings-button">Lưu thay đổi</button></div>
            </form>
        </section>

        <section class="settings-card" id="password">
            <div class="settings-card-heading">
                <div><h2>Đổi mật khẩu</h2><p>Nhập mật khẩu hiện tại để xác nhận thay đổi bảo mật.</p></div>
            </div>
            <form action="?controller=setting&action=password" method="POST" class="settings-form">
                <div class="settings-fields">
                    <label class="settings-field settings-field-wide">
                        <span>Mật khẩu hiện tại <b>*</b></span>
                        <input type="password" name="current_password" required autocomplete="current-password">
                    </label>
                    <label class="settings-field">
                        <span>Mật khẩu mới <b>*</b></span>
                        <input type="password" name="new_password" minlength="8" required autocomplete="new-password">
                        <small>Tối thiểu 8 ký tự.</small>
                    </label>
                    <label class="settings-field">
                        <span>Xác nhận mật khẩu mới <b>*</b></span>
                        <input type="password" name="confirm_password" minlength="8" required autocomplete="new-password">
                    </label>
                </div>
                <div class="settings-actions"><button type="submit" class="settings-button settings-button-secondary">Cập nhật mật khẩu</button></div>
            </form>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/../layout/footer-dashboard.php'; ?>
