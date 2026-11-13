<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>
<div class="dashboard-layout">
    <div class="dashboard-container admin-page">
        <section class="admin-header">
            <div>
                <h1>Phiên Chat</h1>
                <p>Chi tiết các tin nhắn trong phiên.</p>
            </div>
            <div class="admin-header-actions">
                <a href="?controller=admin&action=chatbot" class="btn btn-secondary">Quay lại</a>
            </div>
        </section>
        <section class="chat-session-messages">
            <?php if (empty($messages)) { echo '<p>Không có tin nhắn.</p>'; } else { foreach ($messages as $m) { ?>
                <div class="chat-msg <?php echo htmlspecialchars($m['role']); ?>">
                    <div class="chat-meta"><?php echo htmlspecialchars($m['role']); ?> • <?php echo htmlspecialchars($m['created_at']); ?></div>
                    <div class="chat-content"><?php echo nl2br(htmlspecialchars($m['content'])); ?></div>
                </div>
            <?php } } ?>
        </section>
    </div>
</div>
<?php require_once 'View/layout/footer-dashboard.php'; ?>
