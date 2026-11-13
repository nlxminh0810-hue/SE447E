<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<style>
    .admin-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }
    .admin-kpi-card {
        background: #ffffff;
        border: 1px solid #eaedf2;
        border-radius: 10px;
        padding: 24px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .admin-kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    }
    .admin-kpi-card span {
        display: block;
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .admin-kpi-card strong {
        display: block;
        font-size: 32px;
        color: #2c3e50;
        font-weight: 700;
    }
    .admin-shortcut-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }
    .admin-shortcut-card {
        background: #ffffff;
        border: 1px solid #eaedf2;
        border-radius: 10px;
        padding: 24px;
        text-decoration: none;
        color: #333;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .admin-shortcut-card:hover {
        border-color: #3498db;
        box-shadow: 0 6px 15px rgba(52, 152, 219, 0.15);
    }
    .admin-shortcut-card h3 {
        margin-top: 0;
        margin-bottom: 10px;
        font-size: 18px;
        color: #2c3e50;
    }
    .admin-shortcut-card p {
        font-size: 14px;
        color: #6c757d;
        flex-grow: 1;
        margin-bottom: 16px;
        line-height: 1.5;
    }
    .admin-shortcut-card span {
        font-size: 14px;
        font-weight: 600;
        color: #3498db;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .section-title {
        font-size: 20px;
        margin-bottom: 20px;
        color: #2c3e50;
        font-weight: 600;
    }
</style>

<div class="dashboard-layout">
    <div class="dashboard-container admin-page">
        <section class="admin-header">
            <div>
                <h1>Admin Dashboard</h1>
                <p>Quản lý người dùng, khóa học và theo dõi vận hành hệ thống ENGLISHAI.</p>
            </div>
            <div class="admin-header-actions">
                <a href="?controller=admin&action=users" class="btn btn-primary">Quản lý User</a>
                <a href="?controller=admin&action=courses" class="btn btn-secondary">Quản lý Khóa học</a>
            </div>
        </section>

        <h2 class="section-title">Tổng quan hệ thống</h2>
        <section class="admin-kpi-grid">
            <article class="admin-kpi-card">
                <span>Tổng Users</span>
                <strong><?php echo number_format((int) ($adminStats['total_users'] ?? 0)); ?></strong>
            </article>
            <article class="admin-kpi-card">
                <span>Users hoạt động</span>
                <strong><?php echo number_format((int) ($adminStats['active_users'] ?? 0)); ?></strong>
            </article>
            <article class="admin-kpi-card">
                <span>Tổng khóa học</span>
                <strong><?php echo number_format((int) ($adminStats['total_courses'] ?? 0)); ?></strong>
            </article>
            <article class="admin-kpi-card">
                <span>Khóa học đã publish</span>
                <strong><?php echo number_format((int) ($adminStats['published_courses'] ?? 0)); ?></strong>
            </article>
            <article class="admin-kpi-card">
                <span>Đăng ký mới (7 ngày)</span>
                <strong><?php echo number_format((int) ($adminStats['new_signups_week'] ?? 0)); ?></strong>
            </article>
            <article class="admin-kpi-card">
                <span>Tỷ lệ hoàn thành</span>
                <strong><?php echo number_format((int) ($adminStats['completion_rate'] ?? 0)); ?>%</strong>
            </article>
            <article class="admin-kpi-card">
                <span>Tổng doanh thu</span>
                <strong><?php echo number_format((float) ($adminStats['total_revenue'] ?? 0), 0, ',', '.'); ?> VNĐ</strong>
            </article>
        </section>

        <h2 class="section-title">CHI TIẾT HỆ THỐNG</h2>
        <section class="admin-shortcut-grid">
            <a class="admin-shortcut-card" href="?controller=admin&action=users">
                <h3>👤 Quản lý User</h3>
                <p>Xem danh sách, phân quyền, khóa/mở khóa tài khoản học viên và giảng viên.</p>
                <span>Xem chi tiết &rarr;</span>
            </a>
            <a class="admin-shortcut-card" href="?controller=admin&action=courses">
                <h3>📚 Quản lý Khóa học</h3>
                <p>Cập nhật thông tin khóa học, trạng thái xuất bản, giá bán và nội dung.</p>
                <span>Xem chi tiết &rarr;</span>
            </a>
            <a class="admin-shortcut-card" href="?controller=admin&action=tests">
                <h3>📝 Quản lý Đề thi</h3>
                <p>Quản lý danh sách các bài kiểm tra, câu hỏi và xem kết quả của học viên.</p>
                <span>Xem chi tiết &rarr;</span>
            </a>
            <a class="admin-shortcut-card" href="?controller=admin&action=reports">
                <h3>📊 Báo cáo & Thống kê</h3>
                <p>Xem số liệu tổng quan chi tiết và xuất báo cáo dữ liệu của hệ thống.</p>
                <span>Xem chi tiết &rarr;</span>
            </a>
            <a class="admin-shortcut-card" href="?controller=admin&action=chatbot">
                <h3>🤖 Quản lý Chatbot</h3>
                <p>Theo dõi lịch sử hội thoại và cấu hình của trợ lý ảo AI với học viên.</p>
                <span>Xem chi tiết &rarr;</span>
            </a>
            <a class="admin-shortcut-card" href="?controller=admin&action=revenue">
                <h3>💰 Quản lý Doanh thu</h3>
                <p>Theo dõi doanh thu, lịch sử giao dịch và thống kê thanh toán khóa học.</p>
                <span>Xem chi tiết &rarr;</span>
            </a>
        </section>
    </div>
</div>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
