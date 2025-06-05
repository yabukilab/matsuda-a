-- (A2) データベース作成（既存の場合はスキップ）
CREATE DATABASE IF NOT EXISTS mydb 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

-- (A3) ユーザー作成（安全な方法で）
DROP USER IF EXISTS 'testuser'@'localhost'; -- 既存ユーザー削除
CREATE USER 'testuser'@'localhost' IDENTIFIED BY 'pass';
GRANT ALL PRIVILEGES ON mydb.* TO 'testuser'@'localhost';

-- (A4)mydbを使うことを宣言する．
use mydb;

-- ユーザーテーブル
CREATE TABLE users (
    student_id CHAR(7) PRIMARY KEY,
    is_admin BOOLEAN DEFAULT FALSE
);

-- スレッドテーブル
CREATE TABLE threads (
    thread_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    created_by CHAR(7) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(student_id)
);

-- コメントテーブル
CREATE TABLE comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    thread_id INT NOT NULL,
    student_id CHAR(7) NOT NULL,
    content TEXT NOT NULL,
    file_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (thread_id) REFERENCES threads(thread_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(student_id)
);

-- 管理者ユーザーの登録 (学籍番号9877389)
INSERT INTO users (student_id, is_admin) VALUES ('9877389', TRUE);

ALTER TABLE comments MODIFY file_path TEXT;

-- データベースの変更
CREATE TABLE uploaded_folders (
    folder_id INT AUTO_INCREMENT PRIMARY KEY,
    comment_id INT NOT NULL,
    folder_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comment_id) REFERENCES comments(comment_id) ON DELETE CASCADE
);