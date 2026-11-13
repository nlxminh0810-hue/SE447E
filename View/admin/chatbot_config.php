<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<div class="admin-page">
    <h1>Chatbot Configuration</h1>

    <form method="post" action="?controller=admin&action=chatbot_config_save">
        <div class="form-group">
            <label>Provider name (e.g. OpenAI or Gemini)</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($cfg['name'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>API Key</label>
            <input type="text" name="api_key" class="form-control" value="<?php echo htmlspecialchars($cfg['api_key'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Model</label>
            <input type="text" name="model" class="form-control" value="<?php echo htmlspecialchars($cfg['model'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>System prompt</label>
            <textarea name="system_prompt" class="form-control" rows="4"><?php echo htmlspecialchars($cfg['system_prompt'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label><input type="checkbox" name="is_active" value="1" <?php echo (!empty($cfg['is_active']) ? 'checked' : ''); ?>> Active</label>
        </div>

        <button class="btn btn-primary">Save</button>
    </form>
</div>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
