<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>
<div class="dashboard-layout">
    <div class="dashboard-container admin-page">
        <section class="admin-header">
            <div>
                <h1><?php echo isset($question['id']) ? 'Sửa câu hỏi' : 'Tạo câu hỏi mới'; ?></h1>
            </div>
            <div class="admin-header-actions">
                <a href="?controller=admin&action=questions&test_id=<?php echo (int)($_GET['test_id'] ?? ($question['test_id'] ?? '')); ?>" class="btn btn-secondary">Quay lại</a>
            </div>
        </section>

        <section class="admin-form-wrap">
            <form method="post">
                <input type="hidden" name="test_id" value="<?php echo (int)($_GET['test_id'] ?? ($question['test_id'] ?? '')); ?>" />
                <div class="form-row">
                    <label>Nội dung câu hỏi</label>
                    <textarea name="content" rows="4"><?php echo htmlspecialchars($question['content'] ?? ''); ?></textarea>
                </div>
                <div class="form-row">
                    <label>Loại</label>
                    <select name="type">
                        <option value="single_choice" <?php if (($question['type'] ?? '') == 'single_choice') echo 'selected'; ?>>Single choice</option>
                        <option value="multiple_choice" <?php if (($question['type'] ?? '') == 'multiple_choice') echo 'selected'; ?>>Multiple choice</option>
                    </select>
                </div>
                <div class="form-row">
                    <label>Thứ tự</label>
                    <input type="number" name="order_index" value="<?php echo (int)($question['order_index'] ?? 0); ?>" />
                </div>

                <div class="form-row">
                    <label>Đáp án (mỗi dòng một đáp án; đánh dấu radio tương ứng là đáp án đúng)</label>
                    <div id="answers">
                        <?php $existing = $answers ?? []; if (empty($existing)) { $existing = [['content'=>'','is_correct'=>0],['content'=>'','is_correct'=>0],['content'=>'','is_correct'=>0],['content'=>'','is_correct'=>0]]; }
                        foreach ($existing as $i => $a): ?>
                            <div class="answer-row">
                                <input type="text" name="answer_content[]" value="<?php echo htmlspecialchars($a['content'] ?? ''); ?>" />
                                <label>Đúng <input type="checkbox" name="answer_correct[<?php echo $i; ?>]" value="1" <?php if (!empty($a['is_correct'])) echo 'checked'; ?> /></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div><button type="button" onclick="addAnswer()" class="btn">Thêm đáp án</button></div>
                </div>

                <div class="form-row">
                    <button class="btn btn-primary" type="submit">Lưu</button>
                </div>
            </form>
        </section>
    </div>
</div>
<?php require_once 'View/layout/footer-dashboard.php'; ?>

<script>
function addAnswer(){
    var container = document.getElementById('answers');
    var idx = container.children.length;
    var div = document.createElement('div');
    div.className = 'answer-row';
    div.innerHTML = '<input type="text" name="answer_content[]" value="" /> <label>Đúng <input type="checkbox" name="answer_correct['+idx+']" value="1" /></label>';
    container.appendChild(div);
}
</script>
