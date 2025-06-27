-- (1) ユーザーとデータベースの作成（存在しない場合のみ）
CREATE DATABASE IF NOT EXISTS mydb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'testuser'@'localhost' IDENTIFIED BY 'pass';
GRANT ALL PRIVILEGES ON mydb.* TO 'testuser'@'localhost';

-- (2) 使用するデータベースの選択
USE mydb;

-- (3) テーブル削除（順番に注意）
DROP TABLE IF EXISTS uploaded_files;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS threads;
DROP TABLE IF EXISTS users;

-- (4) テーブル定義

-- ユーザー情報
CREATE TABLE users (
    student_id CHAR(7) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    is_admin BOOLEAN NOT NULL DEFAULT FALSE
);

-- スレッド情報
CREATE TABLE threads (
    thread_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    created_by CHAR(7) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(student_id)
);
-- コメント情報
CREATE TABLE comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    thread_id INT NOT NULL,
    student_id CHAR(7) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (thread_id) REFERENCES threads(thread_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(student_id)
);

-- アップロードファイル情報（file_dataをLONGTEXTに変更）
CREATE TABLE uploaded_files (
    file_id INT AUTO_INCREMENT PRIMARY KEY,
    comment_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) DEFAULT NULL,
    file_size INT DEFAULT 0,
    file_data LONGTEXT NOT NULL,
    is_zip BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comment_id) REFERENCES comments(comment_id) ON DELETE CASCADE
);

-- (5) 管理者ユーザー追加（bcryptハッシュ済み）
INSERT INTO users (student_id, name, password_hash, is_admin)
VALUES (
    '9877389',
    'ctrlPhoenix',
    '$2y$10$n8GZzSD0jxFdSEknTD/3o.XSHtACIbfpKhyKNjfQfV2LO94wZGOqO',
    TRUE
)
ON DUPLICATE KEY UPDATE name = VALUES(name), password_hash = VALUES(password_hash), is_admin = VALUES(is_admin);
