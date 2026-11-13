<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân</title>
    <!-- Bạn có thể thêm CSS framework như Bootstrap để trang đẹp hơn -->
    <style>
        body { font-family: sans-serif; margin: 2em; }
        .form-group { margin-bottom: 1em; }
        label { display: block; margin-bottom: 0.5em; }
        input, textarea { width: 300px; padding: 0.5em; }
        .success-message { color: green; border: 1px solid green; padding: 1em; margin-bottom: 1em; }
    </style>
</head>
<body>

    <h1>Thông tin cá nhân</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="success-message">Cập nhật thông tin thành công!</div>
    <?php endif; ?>

    <!-- Form sẽ gửi dữ liệu đến file xử lý update -->
    <form action="/profile_update.php" method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>" disabled>
            <small>Email không thể thay đổi.</small>
        </div>

        <div class="form-group">
            <label for="full_name">Họ và tên</label>
            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($data['full_name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="phone_number">Số điện thoại</label>
            <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($data['phone_number'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="address">Địa chỉ</label>
            <input type="text" id="address" name="address" value="<?= htmlspecialchars($data['address'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="bio">Giới thiệu bản thân</label>
            <textarea id="bio" name="bio" rows="4"><?= htmlspecialchars($data['bio'] ?? '') ?></textarea>
        </div>

        <button type="submit">Cập nhật</button>
    </form>

</body>
</html>