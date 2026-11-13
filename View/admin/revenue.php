<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<style>
    .revenue-kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .revenue-kpi-card { background: #fff; padding: 24px; border-radius: 10px; border: 1px solid #eaedf2; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.02); }
    .revenue-kpi-card span { color: #6c757d; font-size: 14px; text-transform: uppercase; font-weight: 600; }
    .revenue-kpi-card strong { display: block; font-size: 28px; color: #3498db; margin-top: 10px; font-weight: 700; }
    .tag { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
    .status-active, .status-completed { background: #e8f8ef; color: #52c41a; }
    .status-pending { background: #fff7e6; color: #fa8c16; }
    .status-other { background: #f0f0f0; color: #6c757d; }
</style>

<div class="dashboard-layout">
    <div class="dashboard-container admin-page">
        <section class="admin-header">
            <div>
                <h1>Quản lý Doanh thu</h1>
                <p>Theo dõi biểu đồ doanh thu và chi tiết các giao dịch.</p>
            </div>
            <div class="admin-header-actions">
                <a href="?controller=admin&amp;action=export_revenue" class="btn btn-primary">Xuất báo cáo CSV</a>
                <a href="?controller=admin&amp;action=index" class="btn btn-secondary">Quay lại Dashboard</a>
            </div>
        </section>

        <section class="revenue-kpi-grid">
            <article class="revenue-kpi-card">
                <span>Tổng Doanh Thu</span>
                <strong><?php echo number_format((float) ($totalRevenue ?? 0), 0, ',', '.'); ?> VNĐ</strong>
            </article>
            <article class="revenue-kpi-card">
                <span>Doanh Thu Tháng Này</span>
                <strong><?php echo number_format((float) ($monthRevenue ?? 0), 0, ',', '.'); ?> VNĐ</strong>
            </article>
            <article class="revenue-kpi-card">
                <span>Tổng Số Giao Dịch</span>
                <strong><?php echo number_format((int) ($totalTransactions ?? 0)); ?></strong>
            </article>
        </section>

        <section class="admin-toolbar">
            <div class="admin-toolbar-title">
                <strong>Biểu đồ Doanh thu</strong>
            </div>
            <div class="admin-toolbar-actions">
                <select aria-label="Lọc thời gian" onchange="window.location.href='?controller=admin&amp;action=revenue&amp;filter=' + this.value" style="padding: 8px 12px; border: 1px solid #eaedf2; border-radius: 5px; background: #fff; cursor: pointer;">
                    <option value="7_days" <?php echo ($filter === '7_days') ? 'selected' : ''; ?>>7 ngày qua</option>
                    <option value="30_days" <?php echo ($filter === '30_days') ? 'selected' : ''; ?>>30 ngày qua</option>
                    <option value="this_month" <?php echo ($filter === 'this_month') ? 'selected' : ''; ?>>Tháng này</option>
                    <option value="this_year" <?php echo ($filter === 'this_year') ? 'selected' : ''; ?>>Năm nay</option>
                    <option value="all_time" <?php echo ($filter === 'all_time') ? 'selected' : ''; ?>>Toàn thời gian</option>
                </select>
            </div>
        </section>

        <section class="chart-container" style="position: relative; height: 350px; background: #ffffff; padding: 24px; border-radius: 10px; border: 1px solid #eaedf2; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
            <canvas id="revenueChart"></canvas>
        </section>

        <section class="admin-toolbar">
            <div class="admin-toolbar-title">
                <strong>Lịch sử Giao dịch</strong>
                <span><?php echo count($transactions ?? []); ?> giao dịch</span>
            </div>
            <div class="admin-toolbar-actions">
                <input type="text" id="searchTxn" placeholder="Tìm mã GD, người mua, khóa học..." onkeyup="filterTransactions()" style="padding: 8px 12px; border: 1px solid #eaedf2; border-radius: 5px; width: 260px;">
            </div>
        </section>

        <section class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã GD</th>
                        <th>Người mua</th>
                        <th>Sản phẩm</th>
                        <th>Số tiền (VNĐ)</th>
                        <th>Ngày giao dịch</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="txnTableBody">
                    <?php if (!empty($transactions)): ?>
                        <?php foreach ($transactions as $txn): ?>
                            <?php
                                $status = strtolower($txn['status'] ?? '');
                                $statusClass = in_array($status, ['active', 'completed']) ? 'status-' . $status : (($status === 'pending') ? 'status-pending' : 'status-other');
                                $statusLabel = in_array($status, ['active', 'completed']) ? 'Hoàn thành' : (($status === 'pending') ? 'Đang xử lý' : ucfirst($status));
                            ?>
                            <tr>
                                <td>#<?php echo strtoupper(substr($txn['source'] ?? 'gd', 0, 2)) . '-' . (int) ($txn['ref_id'] ?? 0); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($txn['full_name'] ?? 'N/A'); ?></strong>
                                    <div style="font-size: 12px; color: #6c757d;"><?php echo htmlspecialchars($txn['email'] ?? ''); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($txn['item_title'] ?? '(Không xác định)'); ?></td>
                                <td><strong>+<?php echo number_format((float) ($txn['amount'] ?? 0), 0, ',', '.'); ?></strong></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($txn['created_at'] ?? 'now'))); ?></td>
                                <td><span class="tag <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align: center; color: #6c757d; padding: 24px;">Chưa có dữ liệu giao dịch.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');

        // Dữ liệu biểu đồ được truyền từ Controller
        const chartLabels = <?php echo json_encode($chartLabels ?? []); ?>;
        const chartValues = <?php echo json_encode($chartValues ?? []); ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: chartValues,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3498db',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { color: '#eaedf2' } }, x: { grid: { display: false } } }
            }
        });
    });

    // Tìm kiếm client-side trong danh sách giao dịch
    function filterTransactions() {
        let input = document.getElementById("searchTxn");
        let filter = input.value.toLowerCase();
        let tbody = document.getElementById("txnTableBody");
        let trs = tbody.getElementsByTagName("tr");

        for (let i = 0; i < trs.length; i++) {
            let tr = trs[i];
            if (tr.getElementsByTagName("td").length > 1) {
                let textContent = tr.textContent || tr.innerText;
                tr.style.display = (textContent.toLowerCase().indexOf(filter) > -1) ? "" : "none";
            }
        }
    }
</script>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
