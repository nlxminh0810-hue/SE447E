<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<?php
    $paymentSuccess = $_SERVER['REQUEST_METHOD'] === 'POST';
    $user = class_exists('Auth') && Auth::isLoggedIn() ? Auth::getUser() : null;
?>

<div class="dashboard-layout">
    <div class="dashboard-container checkout-page">
        <section class="checkout-header">
            <div>
                <div class="course-tag">Thanh toán</div>
                <h1>Xác nhận đăng ký khoá học</h1>
                <p>Hoàn tất thanh toán để bắt đầu lộ trình học của bạn.</p>
            </div>
            <a href="?controller=course&action=detail&course=<?php echo urlencode($course['slug'] ?? ($course['id'] ?? '')); ?>" class="btn btn-secondary">Xem chi tiết lại</a>
        </section>

        <?php if ($paymentSuccess): ?>
            <div class="checkout-success" style="padding: 15px; background: #e6ffed; color: #0a6b2e; border: 1px solid #b7f0c9; border-radius: 5px; margin-bottom: 20px;">
                Thanh toán của bạn đã được xác nhận thành công! Bạn hiện có thể truy cập nội dung khoá học. 
                <br><br>
                <a href="?controller=lesson&action=index&course_id=<?php echo (int)($course['id'] ?? 0); ?>" class="btn btn-primary" style="display: inline-block;">Bắt đầu học ngay</a>
            </div>
        <?php endif; ?>

        <div class="checkout-grid">
            <section class="checkout-form-card">
                <h2>Thông tin thanh toán</h2>
                <form method="post" action="?controller=course&action=checkout&course=<?php echo urlencode($course['slug'] ?? ($course['id'] ?? '')); ?>" class="checkout-form">
                    <div class="form-row">
                        <label>Họ và tên</label>
                        <input type="text" name="customer_name" placeholder="Nhập họ tên của bạn" value="<?php echo htmlspecialchars($user['full_name'] ?? $user['name'] ?? $user['username'] ?? ''); ?>" required>
                    </div>
                    <div class="form-row">
                        <label>Email</label>
                        <input type="email" name="customer_email" placeholder="Nhập email nhận xác nhận" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-row">
                        <label>Số điện thoại</label>
                        <input type="text" name="customer_phone" placeholder="Nhập số điện thoại" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-row">
                        <label>Phương thức thanh toán</label>
                        <div class="payment-methods">
                                <label class="payment-method-option">
                                    <input type="radio" name="payment_method" checked>
                                    <img src="public/images/chuyenkhoan.jpg" alt="Chuyển khoản">
                                    <span>Chuyển khoản</span>
                                </label>
                                <label class="payment-method-option">
                                    <input type="radio" name="payment_method">
                                    <img src="public/images/momo.jpg" alt="MoMo">
                                    <span>MoMo</span>
                                </label>
                                <label class="payment-method-option">
                                    <input type="radio" name="payment_method">
                                    <img src="public/images/nganhang.jpg" alt="Thẻ ngân hàng">
                                    <span>Thẻ ngân hàng</span>
                                </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary checkout-submit">Xác nhận thanh toán</button>
                </form>
            </section>

            <aside class="checkout-summary-card">
                <h2>Tóm tắt khoá học</h2>
                <div class="checkout-course-card">
                    <img src="<?php echo htmlspecialchars(asset_image_url($course['image'] ?? $course['thumbnail_url'] ?? 'default-course.jpg')); ?>" alt="<?php echo htmlspecialchars($course['title'] ?? ''); ?>">
                    <div>
                        <div class="course-tag"><?php echo htmlspecialchars($course['tag'] ?? $course['category'] ?? 'Khóa học'); ?></div>
                        <h3><?php echo htmlspecialchars($course['title'] ?? ''); ?></h3>
                        <p><?php echo htmlspecialchars($course['level'] ?? 'Mọi trình độ'); ?></p>
                    </div>
                </div>

                <ul class="summary-list">
                    <li><span>Thời lượng</span><strong><?php echo htmlspecialchars($course['duration'] ?? 'Không giới hạn'); ?></strong></li>
                    <li><span>Số bài học</span><strong><?php echo (int) ($course['lessons'] ?? 0); ?></strong></li>
                    <li><span>Ghi chú</span><strong>Học online</strong></li>
                </ul>

                <div class="checkout-price-row">
                    <span>Tổng thanh toán</span>
                    <strong>
                        <?php 
                            $price = $course['price'] ?? 0;
                            echo is_numeric($price) ? number_format((float)$price, 0, ',', '.') . ' VNĐ' : htmlspecialchars($price); 
                        ?>
                    </strong>
                </div>
            </aside>
        </div>
    </div>
</div>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
