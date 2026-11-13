<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>
<div class="dashboard-layout">
    <div class="dashboard-container admin-page">
        <section class="admin-header">
            <div>
                <h1>Báo cáo & Thống kê</h1>
                <p>Các số liệu tổng quan về hệ thống.</p>
            </div>
        </section>

        <section class="reports-grid">
            <div class="report-card">
                <h3>Người dùng</h3>
                <div class="report-value"><?php echo (int)$reports['users_total']; ?></div>
            </div>
            <div class="report-card">
                <h3>Khóa học</h3>
                <div class="report-value"><?php echo (int)$reports['courses_total']; ?></div>
            </div>
            <div class="report-card">
                <h3>Đề thi</h3>
                <div class="report-value"><?php echo (int)$reports['tests_total']; ?></div>
            </div>
            <div class="report-card">
                <h3>Phiên chat</h3>
                <div class="report-value"><?php echo (int)$reports['chat_sessions']; ?></div>
            </div>
            <div class="report-card">
                <h3>Điểm TB bài kiểm tra</h3>
                <div class="report-value"><?php echo htmlspecialchars($reports['avg_test_score']); ?></div>
            </div>
        </section>

        <section class="report-actions">
            <a href="?controller=admin&action=export_users" class="btn btn-primary">Export Users CSV</a>
        </section>
    </div>
</div>
<?php require_once 'View/layout/footer-dashboard.php'; ?>
