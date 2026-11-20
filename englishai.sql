-- ENGLISHAI initial database schema and sample data.
-- Import this file into MySQL/MariaDB (for example through phpMyAdmin).
-- The script creates the database if needed and does not drop existing data.

CREATE DATABASE IF NOT EXISTS englishai
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE englishai;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS roles (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_roles_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(80) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    avatar_url VARCHAR(255) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    last_login DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_roles (
    user_id INT UNSIGNED NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    assigned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, role_id),
    KEY idx_user_roles_role (role_id),
    CONSTRAINT fk_user_roles_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_user_roles_role FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_profiles (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    bio TEXT NULL,
    phone_number VARCHAR(20) NULL,
    address TEXT NULL,
    avatar_url VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_user_profiles_user (user_id),
    CONSTRAINT fk_user_profiles_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS courses (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    teacher_id INT UNSIGNED NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    level VARCHAR(80) NULL,
    thumbnail_url VARCHAR(255) NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_courses_title (title),
    KEY idx_courses_teacher (teacher_id),
    KEY idx_courses_status_updated (status, updated_at),
    CONSTRAINT fk_courses_teacher FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Existing databases can apply course-video support without recreating tables.
ALTER TABLE courses ADD COLUMN IF NOT EXISTS promo_video_url VARCHAR(500) NULL AFTER thumbnail_url;

CREATE TABLE IF NOT EXISTS lessons (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    course_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    video_url VARCHAR(500) NULL,
    content LONGTEXT NULL,
    duration VARCHAR(50) NULL,
    type VARCHAR(50) NULL,
    progress TINYINT UNSIGNED NOT NULL DEFAULT 0,
    topics LONGTEXT NULL,
    notes TEXT NULL,
    order_index INT NOT NULL DEFAULT 0,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_lessons_course_order (course_id, status, order_index),
    CONSTRAINT fk_lessons_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS course_enrollments (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    course_id INT UNSIGNED NOT NULL,
    status ENUM('active', 'completed', 'cancelled', 'pending') NOT NULL DEFAULT 'active',
    enrolled_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_course_enrollment_user_course (user_id, course_id),
    KEY idx_course_enrollments_course_status (course_id, status),
    CONSTRAINT fk_course_enrollments_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_course_enrollments_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lesson_progress (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    lesson_id INT UNSIGNED NOT NULL,
    completed TINYINT(1) NOT NULL DEFAULT 0,
    completed_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_lesson_progress_user_lesson (user_id, lesson_id),
    CONSTRAINT fk_lesson_progress_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_lesson_progress_lesson FOREIGN KEY (lesson_id) REFERENCES lessons (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tests (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    course_id INT UNSIGNED NOT NULL,
    lesson_id INT UNSIGNED NULL,
    title VARCHAR(200) NOT NULL,
    duration_minutes INT UNSIGNED NOT NULL DEFAULT 15,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_tests_course_created (course_id, created_at),
    KEY idx_tests_lesson (lesson_id),
    CONSTRAINT fk_tests_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE,
    CONSTRAINT fk_tests_lesson FOREIGN KEY (lesson_id) REFERENCES lessons (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS questions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    test_id INT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    type VARCHAR(50) NOT NULL DEFAULT 'single_choice',
    order_index INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_questions_test_order (test_id, order_index),
    CONSTRAINT fk_questions_test FOREIGN KEY (test_id) REFERENCES tests (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS answers (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    question_id INT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_answers_question (question_id),
    CONSTRAINT fk_answers_question FOREIGN KEY (question_id) REFERENCES questions (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS test_results (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    test_id INT UNSIGNED NOT NULL,
    score DECIMAL(6,2) NOT NULL DEFAULT 0.00,
    total_questions INT UNSIGNED NOT NULL DEFAULT 0,
    correct_answers INT UNSIGNED NOT NULL DEFAULT 0,
    answers LONGTEXT NULL,
    taken_at DATETIME NULL,
    submitted_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_test_results_test_created (test_id, created_at),
    KEY idx_test_results_user_created (user_id, created_at),
    CONSTRAINT fk_test_results_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_test_results_test FOREIGN KEY (test_id) REFERENCES tests (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS chat_sessions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    session_key VARCHAR(100) NOT NULL,
    user_id INT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL DEFAULT 'Cuoc tro chuyen moi',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_chat_sessions_key (session_key),
    KEY idx_chat_sessions_user_updated (user_id, updated_at),
    CONSTRAINT fk_chat_sessions_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS chat_messages (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    session_id INT UNSIGNED NOT NULL,
    role VARCHAR(30) NOT NULL,
    content LONGTEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_chat_messages_session_created (session_id, created_at),
    CONSTRAINT fk_chat_messages_session FOREIGN KEY (session_id) REFERENCES chat_sessions (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS chatbot_configs (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    system_prompt TEXT NULL,
    model VARCHAR(150) NULL,
    api_key TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_chatbot_configs_active (is_active, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS service_packages (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    duration_days INT UNSIGNED NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_service_packages_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS transactions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    package_id INT UNSIGNED NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(50) NULL,
    reference_code VARCHAR(120) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_transactions_status_created (status, created_at),
    KEY idx_transactions_user (user_id),
    KEY idx_transactions_package (package_id),
    CONSTRAINT fk_transactions_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_transactions_package FOREIGN KEY (package_id) REFERENCES service_packages (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    course_id INT UNSIGNED NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(50) NULL,
    transaction_code VARCHAR(120) NULL,
    paid_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_payments_user_created (user_id, created_at),
    CONSTRAINT fk_payments_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_payments_course FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vocabulary (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    word VARCHAR(150) NOT NULL,
    meaning TEXT NOT NULL,
    pronunciation VARCHAR(150) NULL,
    example TEXT NULL,
    level VARCHAR(80) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_vocabulary_word (word),
    KEY idx_vocabulary_level (level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO roles (id, name, description) VALUES
    (1, 'admin', 'Quản trị toàn hệ thống'),
    (2, 'student', 'Học viên'),
    (3, 'teacher', 'Giáo viên');

-- Demo account: admin@englishai.com / admin123
INSERT IGNORE INTO users (username, email, password_hash, full_name, status) VALUES
    ('admin', 'admin@englishai.com', '$2y$10$Scmd6i.OFLSZkSkAmJ03J.V3jDbO8.D.bcL1i8Fgl6ncf3Y0aAItG', 'ENGLISHAI Admin', 1);

INSERT IGNORE INTO user_roles (user_id, role_id)
SELECT id, 1 FROM users WHERE email = 'admin@englishai.com';

INSERT IGNORE INTO courses (id, title, description, price, level, thumbnail_url, status) VALUES
    (1, 'TOEIC Intensive', 'Lộ trình luyện TOEIC tập trung vào kỹ năng nghe đọc, mẹo làm bài và tăng tốc độ xử lý đề.', 499000, 'All levels', 'public/images/toeic.jfif', 1),
    (2, 'IELTS Mastery', 'Khóa học chuyên sâu IELTS với chiến lược học theo band, Speaking topics và Writing templates.', 899000, 'Upper-Intermediate', 'public/images/ielts.jfif', 1),
    (3, 'B1 Communicative', 'Củng cố ngữ pháp, từ vựng và giao tiếp ở trình độ B1.', 299000, 'B1', 'public/images/b1.jfif', 1),
    (4, 'B2 Fluency', 'Nâng tốc độ phản xạ và khả năng diễn đạt tiếng Anh ở trình độ B2.', 399000, 'B2', 'public/images/b2.jfif', 1),
    (5, 'Reading Skills', 'Rèn luyện kỹ năng đọc hiểu và từ vựng theo ngữ cảnh.', 199000, 'Skill Practice', 'public/images/reading.jpg', 1),
    (6, 'Listening Skills', 'Luyện nghe theo chủ đề, bắt keyword và hiểu ý chính.', 199000, 'Skill Practice', 'public/images/listening.jpg', 1),
    (7, 'Writing Skills', 'Thực hành cấu trúc câu, đoạn văn và viết học thuật.', 199000, 'Skill Practice', 'public/images/writing.jpg', 1),
    (8, 'Speaking Skills', 'Luyện phản xạ nói, phát âm và giao tiếp tự tin.', 199000, 'Skill Practice', 'public/images/speak.jpg', 1);

INSERT IGNORE INTO lessons (id, course_id, title, description, video_url, content, duration, type, order_index, status) VALUES
    (1, 1, 'Welcome & Course Overview', 'Làm quen với lộ trình học, nền tảng và mục tiêu khóa học.', '', 'Bắt đầu bằng việc xác định mục tiêu TOEIC và cách theo dõi tiến độ học tập.', '12 phút', 'Lesson', 1, 1),
    (2, 1, 'Vocabulary Starter Pack', 'Từ vựng nền tảng theo chủ đề và cách ghi nhớ bằng ngữ cảnh.', '', 'Học từ mới qua ví dụ thực tế và ôn tập thường xuyên.', '18 phút', 'Practice', 2, 1),
    (3, 1, 'Listening Drill: Short Talks', 'Luyện nghe đoạn hội thoại ngắn và nhận biết từ khóa.', 'Nghe nhiều lần, ghi lại từ khóa và kiểm tra ý chính.', '20 phút', 'Audio + Test', 3, 1),
    (4, 1, 'Speaking Challenge', 'Thực hành mẫu câu, phản xạ và phát âm.', 'Trả lời thành tiếng, ghi âm và tự nghe lại để cải thiện.', '15 phút', 'Speaking', 4, 1),
    (5, 2, 'IELTS Study Roadmap', 'Làm quen với lộ trình và các kỹ năng IELTS.', 'Xác định band mục tiêu và chia thời gian luyện tập cho bốn kỹ năng.', '15 phút', 'Lesson', 1, 1),
    (6, 3, 'B1 Grammar and Vocabulary', 'Ôn ngữ pháp và từ vựng cốt lõi trình độ B1.', 'Học cấu trúc câu thông dụng và áp dụng vào giao tiếp hằng ngày.', '20 phút', 'Lesson', 1, 1),
    (7, 4, 'B2 Fluency Practice', 'Mở rộng vốn từ và luyện diễn đạt lưu loát.', 'Thực hành diễn đạt ý kiến bằng câu phức và từ nối phù hợp.', '20 phút', 'Lesson', 1, 1),
    (8, 5, 'Reading for Main Ideas', 'Tìm ý chính và thông tin quan trọng trong bài đọc.', 'Luyện skimming để nắm ý chính trước khi đọc chi tiết.', '15 phút', 'Practice', 1, 1),
    (9, 6, 'Listening for Keywords', 'Nhận biết từ khóa và ý chính khi nghe.', 'Đọc câu hỏi trước, sau đó chú ý từ khóa và từ đồng nghĩa.', '15 phút', 'Practice', 1, 1),
    (10, 7, 'Build a Clear Paragraph', 'Thực hành viết đoạn văn rõ ý và mạch lạc.', 'Sắp xếp câu chủ đề, ý hỗ trợ và câu kết luận thành một đoạn văn.', '20 phút', 'Practice', 1, 1),
    (11, 8, 'Everyday Speaking Practice', 'Luyện nói về các tình huống quen thuộc.', 'Trả lời câu hỏi bằng câu hoàn chỉnh và luyện phát âm rõ ràng.', '15 phút', 'Speaking', 1, 1);

INSERT IGNORE INTO tests (id, course_id, lesson_id, title, duration_minutes) VALUES
    (1, 1, 1, 'TOEIC Foundation Check', 15),
    (2, 2, 5, 'IELTS Study Roadmap Quiz', 15),
    (3, 3, 6, 'B1 Grammar Check', 15),
    (4, 4, 7, 'B2 Fluency Check', 15),
    (5, 5, 8, 'Reading Skills Check', 15),
    (6, 6, 9, 'Listening Skills Check', 15),
    (7, 7, 10, 'Writing Skills Check', 15),
    (8, 8, 11, 'Speaking Skills Check', 15);

INSERT IGNORE INTO questions (id, test_id, content, type, order_index) VALUES
    (1, 1, 'Choose the correct form: She ____ to school every day.', 'single_choice', 1),
    (2, 2, 'Which four skills are assessed in IELTS?', 'single_choice', 1),
    (3, 3, 'Choose the correct sentence.', 'single_choice', 1),
    (4, 4, 'Which phrase is useful for giving an opinion?', 'single_choice', 1),
    (5, 5, 'What is skimming mainly used for?', 'single_choice', 1),
    (6, 6, 'What should you focus on when listening for keywords?', 'single_choice', 1),
    (7, 7, 'What is usually the main purpose of a topic sentence?', 'single_choice', 1),
    (8, 8, 'What is a good way to improve speaking fluency?', 'single_choice', 1);

INSERT IGNORE INTO answers (question_id, content, is_correct) VALUES
    (1, 'go', 0), (1, 'goes', 1), (1, 'going', 0), (1, 'gone', 0),
    (2, 'Reading, Listening, Writing and Speaking', 1), (2, 'Grammar, Math, Science and History', 0), (2, 'Reading only', 0), (2, 'Writing and translation only', 0),
    (3, 'She do not like coffee.', 0), (3, 'She does not like coffee.', 1), (3, 'She does not likes coffee.', 0), (3, 'She not like coffee.', 0),
    (4, 'In my opinion, ...', 1), (4, 'Yesterday at 5 pm ...', 0), (4, 'At the station platform ...', 0), (4, 'Under the table ...', 0),
    (5, 'To understand the general idea quickly', 1), (5, 'To translate every word', 0), (5, 'To memorize the last sentence', 0), (5, 'To avoid reading the passage', 0),
    (6, 'Important words that signal the information requested', 1), (6, 'Every sound equally', 0), (6, 'Only the speaker’s name', 0), (6, 'The background music', 0),
    (7, 'To introduce the main idea of the paragraph', 1), (7, 'To list unrelated facts', 0), (7, 'To repeat the title only', 0), (7, 'To end the essay', 0),
    (8, 'Practice speaking regularly and review your recordings', 1), (8, 'Avoid speaking aloud', 0), (8, 'Memorize words without using them', 0), (8, 'Only study punctuation', 0);

INSERT IGNORE INTO chatbot_configs (name, system_prompt, model, api_key, is_active) VALUES
    ('Gemini', 'You are a helpful English tutor. Explain clearly and encourage the learner to practice.', 'gemini-3.6-flash', NULL, 1);

INSERT IGNORE INTO service_packages (id, name, description, price, duration_days, status) VALUES
    (1, 'ENGLISHAI Premium', 'Gói dịch vụ mẫu; thanh toán chưa được tích hợp.', 0.00, 30, 1);

INSERT IGNORE INTO vocabulary (word, meaning, pronunciation, example, level) VALUES
    ('improve', 'cải thiện', '/ɪmˈpruːv/', 'I want to improve my English.', 'A2'),
    ('confident', 'tự tin', '/ˈkɒnfɪdənt/', 'She feels confident when speaking English.', 'B1'),
    ('opportunity', 'cơ hội', '/ˌɒpəˈtjuːnəti/', 'This course is a good opportunity to practice.', 'B1');
