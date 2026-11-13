<?php

// Chat Controller - Chatbot AI

if (!isset($_SESSION['chat_conversations']) || !is_array($_SESSION['chat_conversations'])) {
    $_SESSION['chat_conversations'] = [];
}

if (!isset($_SESSION['chat_active_id']) || $_SESSION['chat_active_id'] === '') {
    $_SESSION['chat_active_id'] = '';
}

function chat_schema_table_exists() {
    global $db;
    $db->execute("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'chat_sessions'");
    $row = $db->getData();
    return (int)($row['cnt'] ?? 0) > 0;
}

function ensure_chat_schema() {
    global $db;

    if (!chat_schema_table_exists()) {
        return false;
    }

    $columns = [];
    $db->execute("SELECT column_name, is_nullable FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'chat_sessions'");
    while ($row = $db->getData()) {
        $columns[$row['column_name']] = $row;
    }

    if (!isset($columns['session_key'])) {
        $db->execute("ALTER TABLE chat_sessions ADD COLUMN session_key VARCHAR(100) NULL AFTER id");
        $db->execute("UPDATE chat_sessions SET session_key = CONCAT('legacy_', id) WHERE session_key IS NULL OR session_key = ''");
        $db->execute("ALTER TABLE chat_sessions MODIFY session_key VARCHAR(100) NOT NULL");
        $db->execute("ALTER TABLE chat_sessions ADD UNIQUE KEY uq_chat_sessions_key (session_key)");
    }

    if (isset($columns['user_id']) && strtoupper($columns['user_id']['is_nullable']) !== 'YES') {
        $db->execute("ALTER TABLE chat_sessions MODIFY user_id INT NULL");
    }

    if (!isset($columns['updated_at'])) {
        $db->execute("ALTER TABLE chat_sessions ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at");
    }

    return true;
}

function chat_session_row_by_key($sessionKey) {
    global $db;
    $sessionKey = $db->escapeString($sessionKey);
    $db->execute("SELECT * FROM chat_sessions WHERE session_key = '$sessionKey' LIMIT 1");
    return $db->getData();
}

function chat_message_rows_by_session_id($sessionId) {
    global $db;
    $sessionId = (int)$sessionId;
    $db->execute("SELECT role, content, created_at FROM chat_messages WHERE session_id = '$sessionId' ORDER BY created_at ASC, id ASC");
    $messages = [];
    while ($row = $db->getData()) {
        $messages[] = $row;
    }
    return $messages;
}

function ensure_db_conversation($conversationId, $title = 'Cuộc trò chuyện mới') {
    global $db;

    if (!ensure_chat_schema()) {
        return null;
    }

    $conversationId = trim((string)$conversationId);
    if ($conversationId === '') {
        return null;
    }

    $existing = chat_session_row_by_key($conversationId);
    $userId = Auth::isLoggedIn() ? (int)(Auth::getUser()['id'] ?? 0) : null;

    if (!empty($existing['id'])) {
        $safeTitle = $db->escapeString($title);
        $setSql = "title = '{$safeTitle}'";
        if ($userId) {
            $setSql .= ", user_id = '" . (int)$userId . "'";
        }
        $db->execute("UPDATE chat_sessions SET {$setSql} WHERE id = '" . (int)$existing['id'] . "'");
        return $existing;
    }

    $safeSessionKey = $db->escapeString($conversationId);
    $safeTitle = $db->escapeString($title);
    $userIdSql = $userId ? ("'" . (int)$userId . "'") : 'NULL';
    $db->execute("INSERT INTO chat_sessions (session_key, user_id, title) VALUES ('{$safeSessionKey}', {$userIdSql}, '{$safeTitle}')");
    return chat_session_row_by_key($conversationId);
}

function persist_chat_message($sessionDbId, $role, $content) {
    global $db;
    if (!ensure_chat_schema()) {
        return false;
    }

    $sessionDbId = (int)$sessionDbId;
    if ($sessionDbId <= 0) {
        return false;
    }

    $safeRole = $db->escapeString($role);
    $safeContent = $db->escapeString($content);
    return $db->execute("INSERT INTO chat_messages (session_id, role, content) VALUES ('" . (int)$sessionDbId . "', '{$safeRole}', '{$safeContent}')");
}

function hydrate_chat_state_from_db() {
    global $db;

    if (!ensure_chat_schema()) {
        return;
    }

    $sessions = [];
    $knownKeys = array_keys($_SESSION['chat_conversations']);
    $userId = Auth::isLoggedIn() ? (int)(Auth::getUser()['id'] ?? 0) : 0;

    if ($userId > 0) {
        $db->execute("SELECT cs.*, u.full_name FROM chat_sessions cs LEFT JOIN users u ON u.id = cs.user_id WHERE cs.user_id = '$userId' ORDER BY cs.updated_at DESC, cs.id DESC");
        while ($sessionRow = $db->getData()) {
            $sessionKey = $sessionRow['session_key'];
            $messages = chat_message_rows_by_session_id($sessionRow['id']);
            $sessions[$sessionKey] = [
                'id' => $sessionKey,
                'db_session_id' => (int)$sessionRow['id'],
                'title' => $sessionRow['title'],
                'updated_at' => $sessionRow['updated_at'] ?? $sessionRow['created_at'],
                'messages' => $messages,
            ];
        }
    }

    foreach ($knownKeys as $key) {
        if (isset($sessions[$key])) {
            continue;
        }
        $sessionRow = chat_session_row_by_key($key);
        if (!empty($sessionRow['id'])) {
            $sessions[$key] = [
                'id' => $key,
                'db_session_id' => (int)$sessionRow['id'],
                'title' => $sessionRow['title'],
                'updated_at' => $sessionRow['updated_at'] ?? $sessionRow['created_at'],
                'messages' => chat_message_rows_by_session_id($sessionRow['id']),
            ];
        }
    }

    if (!empty($sessions)) {
        $_SESSION['chat_conversations'] = array_replace($_SESSION['chat_conversations'], $sessions);
    }
}

function create_new_conversation() {
    $id = 'c_' . time() . '_' . mt_rand(100, 999);
    $now = date('Y-m-d H:i:s');

    $welcomeMessage = 'Xin chào! Tôi là ENGLISHAI Assistant. Bạn cần luyện kỹ năng nào hôm nay: Reading, Listening, Writing hay Speaking?';
    $dbSession = ensure_db_conversation($id, 'Cuộc trò chuyện mới');

    $_SESSION['chat_conversations'][$id] = [
        'id' => $id,
        'db_session_id' => (int)($dbSession['id'] ?? 0),
        'title' => 'Cuộc trò chuyện mới',
        'updated_at' => $now,
        'messages' => [
            [
                'role' => 'assistant',
                'content' => $welcomeMessage,
                'time' => $now,
            ],
        ],
    ];

    if (!empty($dbSession['id'])) {
        persist_chat_message((int)$dbSession['id'], 'assistant', $welcomeMessage);
    }

    $_SESSION['chat_active_id'] = $id;
    return $id;
}

function delete_chat_session($conversationId) {
    global $db;
    if (!isset($_SESSION['chat_conversations'][$conversationId])) {
        return false;
    }

    $dbId = (int)($_SESSION['chat_conversations'][$conversationId]['db_session_id'] ?? 0);
    
    if ($dbId > 0 && ensure_chat_schema()) {
        $db->execute("DELETE FROM chat_messages WHERE session_id = '{$dbId}'");
        $db->execute("DELETE FROM chat_sessions WHERE id = '{$dbId}'");
    }

    unset($_SESSION['chat_conversations'][$conversationId]);

    if (isset($_SESSION['chat_active_id']) && $_SESSION['chat_active_id'] === $conversationId) {
        $_SESSION['chat_active_id'] = '';
    }
    
    return true;
}

function get_active_conversation_id() {
    $requested = isset($_GET['conv']) ? trim($_GET['conv']) : '';
    if ($requested !== '' && isset($_SESSION['chat_conversations'][$requested])) {
        $_SESSION['chat_active_id'] = $requested;
        return $requested;
    }

    $active = isset($_SESSION['chat_active_id']) ? $_SESSION['chat_active_id'] : '';
    if ($active !== '' && isset($_SESSION['chat_conversations'][$active])) {
        return $active;
    }

    if (!empty($_SESSION['chat_conversations'])) {
        $ids = array_keys($_SESSION['chat_conversations']);
        $_SESSION['chat_active_id'] = $ids[0];
        return $ids[0];
    }

    return create_new_conversation();
}

function build_conversation_title($message) {
    $plain = trim(preg_replace('/\s+/', ' ', $message));
    if ($plain === '') {
        return 'Cuộc trò chuyện mới';
    }
    if (function_exists('mb_substr') && function_exists('mb_strlen')) {
        return mb_substr($plain, 0, 42) . (mb_strlen($plain) > 42 ? '...' : '');
    }
    return substr($plain, 0, 42) . (strlen($plain) > 42 ? '...' : '');
}

function format_chat_display_text($text) {
    static $legacyTexts = [
        'Cuoc tro chuyen moi' => 'Cuộc trò chuyện mới',
        'Xin chao! Toi la ENGLISHAI Assistant. Ban can luyen ky nang nao hom nay: Reading, Listening, Writing hay Speaking?' => 'Xin chào! Tôi là ENGLISHAI Assistant. Bạn cần luyện kỹ năng nào hôm nay: Reading, Listening, Writing hay Speaking?',
    ];

    return $legacyTexts[$text] ?? $text;
}

function get_tutor_system_prompt(array $config = []) {
    if (!empty($config['system_prompt'])) {
        return trim($config['system_prompt']);
    }

    return 'Bạn là gia sư tiếng Anh ENGLISHAI. Nhiệm vụ của bạn là giúp người học tiếng Anh theo hướng dẫn cụ thể, ngắn gọn, rõ ràng và có tính thực hành. Hãy ưu tiên: (1) giải thích dễ hiểu, (2) cho ví dụ tự nhiên, (3) sửa lỗi ngữ pháp/từ vựng nếu có, (4) đề xuất bài tập ngắn nếu phù hợp. Nếu người dùng hỏi về IELTS, TOEIC, Writing, Speaking, Reading, Listening hoặc lập kế hoạch học, hãy trả lời có cấu trúc từng bước. Nếu không chắc chắn, hãy nói rõ và đề nghị cách kiểm tra tiếp.';
}

function get_config_chat_provider(array $config) {
    return !empty($config['name']) && stripos($config['name'], 'open') !== false
        ? 'openai'
        : 'gemini';
}

function get_chat_provider(array $config) {
    $provider = strtolower(trim((string)getenv('AI_PROVIDER')));
    if (in_array($provider, ['gemini', 'openai'], true)) {
        return $provider;
    }

    return get_config_chat_provider($config);
}

function get_chat_model($provider, array $config) {
    $envName = $provider === 'openai' ? 'OPENAI_MODEL' : 'GEMINI_MODEL';
    $envModel = trim((string)getenv($envName));
    if ($envModel !== '') {
        return $envModel;
    }

    if (get_config_chat_provider($config) === $provider && !empty($config['model'])) {
        return trim($config['model']);
    }

    return $provider === 'openai' ? 'gpt-6-luna' : 'gemini-3.6-flash';
}

function record_chat_api_error($provider, $message) {
    global $chatApiErrors;
    if (!isset($chatApiErrors) || !is_array($chatApiErrors)) {
        $chatApiErrors = [];
    }
    $chatApiErrors[] = $provider . ': ' . $message;
}

function normalize_chat_role($role) {
    return $role === 'assistant' ? 'assistant' : 'user';
}

function extract_openai_message_text($data) {
    if (!is_array($data)) {
        return '';
    }

    if (!empty($data['choices'][0]['message']['content']) && is_string($data['choices'][0]['message']['content'])) {
        return trim($data['choices'][0]['message']['content']);
    }

    if (!empty($data['output_text']) && is_string($data['output_text'])) {
        return trim($data['output_text']);
    }

    if (!empty($data['output']) && is_array($data['output'])) {
        foreach ($data['output'] as $outputItem) {
            if (empty($outputItem['content']) || !is_array($outputItem['content'])) {
                continue;
            }

            foreach ($outputItem['content'] as $contentItem) {
                if (!empty($contentItem['text']) && is_string($contentItem['text'])) {
                    return trim($contentItem['text']);
                }
            }
        }
    }

    return '';
}

function extract_gemini_message_text($data) {
    if (!is_array($data)) {
        return '';
    }

    if (!empty($data['candidates'][0]['content']['parts']) && is_array($data['candidates'][0]['content']['parts'])) {
        $parts = [];
        foreach ($data['candidates'][0]['content']['parts'] as $part) {
            if (!empty($part['text']) && is_string($part['text'])) {
                $parts[] = $part['text'];
            }
        }
        if (!empty($parts)) {
            return trim(implode("\n", $parts));
        }
    }

    return '';
}

function load_active_chatbot_config() {
    global $db;
    $db->execute("SELECT * FROM chatbot_configs WHERE is_active = 1 ORDER BY id DESC LIMIT 1");
    return $db->getData();
}

function collect_chat_messages_for_prompt(array $messages) {
    $collected = [];
    foreach ($messages as $message) {
        if (!isset($message['content'])) {
            continue;
        }
        $collected[] = [
            'role' => normalize_chat_role($message['role'] ?? 'user'),
            'content' => (string)$message['content'],
        ];
    }
    return $collected;
}

function call_openai_chat(array $messages, array $config = []) {
    if (empty($config)) {
        $config = load_active_chatbot_config();
    }

    $apiKey = getenv('OPENAI_API_KEY');
    if (!$apiKey && get_chat_provider($config) === 'openai') {
        $apiKey = $config['api_key'] ?? '';
    }
    if (!$apiKey) {
        return '';
    }

    if (!function_exists('curl_init')) {
        record_chat_api_error('OpenAI', 'PHP cURL extension is disabled');
        return '';
    }

    $model = get_chat_model('openai', $config);
    $payloadInput = [];
    $payloadInput[] = [
        'role' => 'system',
        'content' => [
            [
                'type' => 'input_text',
                'text' => get_tutor_system_prompt($config),
            ],
        ],
    ];

    foreach ($messages as $message) {
        if (!isset($message['content'])) {
            continue;
        }

        $payloadInput[] = [
            'role' => normalize_chat_role($message['role'] ?? 'user'),
            'content' => [
                [
                    'type' => 'input_text',
                    'text' => (string) $message['content'],
                ],
            ],
        ];
    }

    $ch = curl_init('https://api.openai.com/v1/responses');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => $model,
            'input' => $payloadInput,
            'max_output_tokens' => 700,
        ]),
        CURLOPT_TIMEOUT => 60,
    ]);

    $rawResponse = curl_exec($ch);
    if ($rawResponse === false) {
        record_chat_api_error('OpenAI', 'Network request failed');
        curl_close($ch);
        return '';
    }

    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode < 200 || $httpCode >= 300) {
        record_chat_api_error('OpenAI', 'API returned HTTP ' . $httpCode);
        return '';
    }

    $decoded = json_decode($rawResponse, true);
    $text = extract_openai_message_text($decoded);
    if ($text === '') {
        record_chat_api_error('OpenAI', 'API response did not contain text');
    }
    return $text;
}

function call_gemini_chat(array $messages, array $config = []) {
    if (empty($config)) {
        $config = load_active_chatbot_config();
    }

    $apiKey = getenv('GEMINI_API_KEY');
    if (!$apiKey) {
        $apiKey = getenv('GOOGLE_GEMINI_API_KEY');
    }
    if (!$apiKey) {
        $apiKey = getenv('GOOGLE_API_KEY');
    }

    if (!$apiKey && get_chat_provider($config) === 'gemini') {
        $apiKey = $config['api_key'] ?? '';
    }

    if (!$apiKey) {
        return '';
    }

    if (!function_exists('curl_init')) {
        record_chat_api_error('Gemini', 'PHP cURL extension is disabled');
        return '';
    }

    $model = get_chat_model('gemini', $config);

    $contents = [];
    foreach (collect_chat_messages_for_prompt($messages) as $message) {
        $contents[] = [
            'role' => $message['role'] === 'assistant' ? 'model' : 'user',
            'parts' => [
                ['text' => $message['content']],
            ],
        ];
    }

    $payload = [
        'system_instruction' => [
            'parts' => [
                ['text' => get_tutor_system_prompt($config)],
            ],
        ],
        'contents' => $contents,
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 700,
        ],
    ];

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($model) . ':generateContent?key=' . rawurlencode($apiKey);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT => 120,
    ]);

    $rawResponse = curl_exec($ch);
    if ($rawResponse === false) {
        $curlError = curl_error($ch);
        record_chat_api_error('Gemini', $curlError !== '' ? 'Lỗi cURL: ' . $curlError : 'Không nhận được phản hồi từ máy chủ.');
        curl_close($ch);
        return '';
    }

    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode < 200 || $httpCode >= 300) {
        record_chat_api_error('Gemini', 'API returned HTTP ' . $httpCode);
        return '';
    }

    $decoded = json_decode($rawResponse, true);
    $text = extract_gemini_message_text($decoded);
    if ($text === '') {
        record_chat_api_error('Gemini', 'API response did not contain text');
    }
    return $text;
}

function generate_ai_response($message, array $conversationMessages = []) {
    $cfg = load_active_chatbot_config();
    global $chatApiErrors;
    $chatApiErrors = [];
    $provider = get_chat_provider($cfg);

    if ($provider === 'openai') {
        $reply = call_openai_chat($conversationMessages, $cfg);
        if ($reply === '') $reply = call_gemini_chat($conversationMessages, $cfg);
    } else {
        $reply = call_gemini_chat($conversationMessages, $cfg);
        if ($reply === '') $reply = call_openai_chat($conversationMessages, $cfg);
    }

    if ($reply !== '') {
        return $reply;
    }

    if (empty($chatApiErrors)) {
        return 'Chưa cấu hình API key cho chatbot. Hãy tạo file .env theo hướng dẫn trong README.md rồi khởi động lại Apache.';
    }

    $lastError = end($chatApiErrors);
    error_log('ENGLISHAI chat API request failed: ' . $lastError);
    if (strpos($lastError, 'cURL extension is disabled') !== false) {
        return 'PHP cURL chưa được bật. Hãy bật extension=curl trong php.ini và khởi động lại Apache.';
    }
    return 'Không gọi được dịch vụ AI (' . $lastError . '). Hãy kiểm tra API key, tên model, hạn mức tài khoản và kết nối Internet.';
}

ensure_chat_schema();
hydrate_chat_state_from_db();

$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch ($action) {
    case 'new': {
        $newId = create_new_conversation();
        header('Location: ?controller=chat&conv=' . urlencode($newId));
        exit;
    }

    case 'send': {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
            exit;
        }

        header('Content-Type: application/json');

        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);

        $message = '';
        $conversationId = '';

        if (is_array($json)) {
            $message = isset($json['message']) ? trim($json['message']) : '';
            $conversationId = isset($json['conversationId']) ? trim($json['conversationId']) : '';
        } else {
            $message = isset($_POST['message']) ? trim($_POST['message']) : '';
            $conversationId = isset($_POST['conversationId']) ? trim($_POST['conversationId']) : '';
        }

        if ($conversationId === '' || !isset($_SESSION['chat_conversations'][$conversationId])) {
            $conversationId = create_new_conversation();
        }

        if ($message === '') {
            echo json_encode([
                'status' => 'error',
                'message' => 'Tin nhắn trống.',
            ]);
            exit;
        }

        $now = date('Y-m-d H:i:s');
        $conversation = &$_SESSION['chat_conversations'][$conversationId];

        if (in_array($conversation['title'], ['Cuộc trò chuyện mới', 'Cuoc tro chuyen moi'], true)) {
            $conversation['title'] = build_conversation_title($message);
            if (!empty($conversation['db_session_id'])) {
                $safeTitle = $db->escapeString($conversation['title']);
                $db->execute("UPDATE chat_sessions SET title = '{$safeTitle}' WHERE id = '" . (int)$conversation['db_session_id'] . "'");
            }
        }

        $conversation['messages'][] = [
            'role' => 'user',
            'content' => $message,
            'time' => $now,
        ];

        if (!empty($conversation['db_session_id'])) {
            persist_chat_message((int)$conversation['db_session_id'], 'user', $message);
        }

        $conversationMessages = array_slice($conversation['messages'], -10);
        $reply = generate_ai_response($message, $conversationMessages);

        $conversation['messages'][] = [
            'role' => 'assistant',
            'content' => $reply,
            'time' => $now,
        ];

        if (!empty($conversation['db_session_id'])) {
            persist_chat_message((int)$conversation['db_session_id'], 'assistant', $reply);
        }

        $conversation['updated_at'] = $now;
        $_SESSION['chat_active_id'] = $conversationId;

        echo json_encode([
            'status' => 'success',
            'conversationId' => $conversationId,
            'title' => $conversation['title'],
            'response' => $reply,
            'updatedAt' => $now,
        ]);
        exit;
    }
    
    case 'delete': {
        $conversationId = isset($_GET['conv']) ? trim($_GET['conv']) : '';
        if ($conversationId !== '') {
            delete_chat_session($conversationId);
        }
        $nextId = get_active_conversation_id();
        header('Location: ?controller=chat&conv=' . urlencode($nextId));
        exit;
    }

    case 'index':
    default: {
        $activeConversationId = get_active_conversation_id();
        $conversations = $_SESSION['chat_conversations'];

        uasort($conversations, function ($a, $b) {
            return strcmp($b['updated_at'], $a['updated_at']);
        });

        $activeConversation = isset($conversations[$activeConversationId])
            ? $conversations[$activeConversationId]
            : reset($conversations);

        require_once 'View/chat/index.php';
        break;
    }
}

?>
