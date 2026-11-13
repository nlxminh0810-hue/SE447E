<?php require_once 'View/layout/header-dashboard.php'; ?>
<?php require_once 'View/layout/sidebar.php'; ?>

<div class="dashboard-layout">
    <div class="dashboard-container chatgpt-page">
        <div class="chat-shell">
            <aside class="chat-conversation-panel">
                <a href="?controller=chat&action=new" class="chat-new-btn">+ Cuộc trò chuyện mới</a>

                <div class="chat-conversation-list">
                    <?php foreach ($conversations as $conv): ?>
                        <a
                            href="?controller=chat&conv=<?php echo urlencode($conv['id']); ?>"
                            class="chat-conversation-item <?php echo ($conv['id'] === $activeConversation['id']) ? 'active' : ''; ?>"
                        >
                            <strong><?php echo htmlspecialchars(format_chat_display_text($conv['title'])); ?></strong>
                            <span><?php echo htmlspecialchars(date('d/m H:i', strtotime($conv['updated_at']))); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </aside>

            <section class="chat-main">
                <header class="chat-main-header">
                    <div>
                        <h1>English Tutor</h1>
                        <p>Gia sư tiếng Anh AI: giải thích, sửa lỗi và tạo bài tập theo mục tiêu của bạn</p>
                    </div>
                    <span class="chat-model-chip">ENGLISHAI Tutor</span>
                </header>

                <div class="chat-message-area" id="chatMessageArea">
                    <?php foreach ($activeConversation['messages'] as $message): ?>
                        <article class="chat-message-row <?php echo $message['role'] === 'user' ? 'user' : 'assistant'; ?>">
                            <div class="chat-avatar"><?php echo $message['role'] === 'user' ? 'U' : 'AI'; ?></div>
                            <div class="chat-bubble">
                                <div class="chat-role"><?php echo $message['role'] === 'user' ? 'Bạn' : 'Assistant'; ?></div>
                                <div class="chat-content"><?php echo nl2br(htmlspecialchars(format_chat_display_text($message['content']))); ?></div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <form id="chatComposer" class="chat-composer" autocomplete="off">
                    <input type="hidden" id="conversationId" value="<?php echo htmlspecialchars($activeConversation['id']); ?>">
                    <textarea
                        id="chatInput"
                        rows="1"
                        maxlength="1200"
                        placeholder="Nhập câu hỏi (VD: Lập kế hoạch IELTS 7 ngày cho tôi)"
                        required
                    ></textarea>
                    <button type="submit" id="chatSendBtn" class="chat-send-btn">Gửi</button>
                </form>

                <div class="chat-footer-note">
                    Enter để gửi, Shift + Enter để xuống dòng.
                </div>
            </section>
        </div>
    </div>
</div>

<script>
(function () {
    const form = document.getElementById('chatComposer');
    const input = document.getElementById('chatInput');
    const sendBtn = document.getElementById('chatSendBtn');
    const messageArea = document.getElementById('chatMessageArea');
    const conversationInput = document.getElementById('conversationId');

    if (!form || !input || !messageArea || !conversationInput) {
        return;
    }

    function scrollToBottom() {
        messageArea.scrollTop = messageArea.scrollHeight;
    }

    function autoResize() {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 180) + 'px';
    }

    function renderMessage(role, content) {
        const row = document.createElement('article');
        row.className = 'chat-message-row ' + (role === 'user' ? 'user' : 'assistant');

        const avatar = document.createElement('div');
        avatar.className = 'chat-avatar';
        avatar.textContent = role === 'user' ? 'U' : 'AI';

        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble';

        const roleNode = document.createElement('div');
        roleNode.className = 'chat-role';
        roleNode.textContent = role === 'user' ? 'Bạn' : 'Assistant';

        const contentNode = document.createElement('div');
        contentNode.className = 'chat-content';
        contentNode.innerHTML = content.replace(/\n/g, '<br>');

        bubble.appendChild(roleNode);
        bubble.appendChild(contentNode);
        row.appendChild(avatar);
        row.appendChild(bubble);
        messageArea.appendChild(row);
        scrollToBottom();
    }

    function renderTyping() {
        const typing = document.createElement('article');
        typing.className = 'chat-message-row assistant';
        typing.id = 'typingRow';

        typing.innerHTML = '<div class="chat-avatar">AI</div><div class="chat-bubble"><div class="chat-role">Assistant</div><div class="chat-typing"><span></span><span></span><span></span></div></div>';
        messageArea.appendChild(typing);
        scrollToBottom();
    }

    function removeTyping() {
        const typing = document.getElementById('typingRow');
        if (typing) {
            typing.remove();
        }
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const text = input.value.trim();
        if (!text) {
            return;
        }

        renderMessage('user', text);
        input.value = '';
        autoResize();
        sendBtn.disabled = true;
        renderTyping();

        try {
            const res = await fetch('?controller=chat&action=send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    message: text,
                    conversationId: conversationInput.value
                })
            });

            const data = await res.json();
            removeTyping();

            if (data.status === 'success') {
                conversationInput.value = data.conversationId;
                renderMessage('assistant', data.response || 'Tôi đã nhận được tin nhắn.');
            } else {
                renderMessage('assistant', 'Xin lỗi, hệ thống đang bận. Bạn thử lại sau nhé.');
            }
        } catch (err) {
            removeTyping();
            renderMessage('assistant', 'Không thể kết nối máy chủ. Bạn kiểm tra lại rồi thử lại nhé.');
        } finally {
            sendBtn.disabled = false;
        }
    });

    input.addEventListener('input', autoResize);
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.requestSubmit();
        }
    });

    scrollToBottom();
})();
</script>

<?php require_once 'View/layout/footer-dashboard.php'; ?>
