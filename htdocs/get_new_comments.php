<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$thread_id = (int)$_GET['thread_id'];
$last_comment_id = (int)$_GET['last_comment_id'];

$stmt = $pdo->prepare("SELECT c.*, u.name AS author_name, u.is_admin 
                      FROM comments c
                      JOIN users u ON c.student_id = u.student_id
                      WHERE c.thread_id = ? AND c.comment_id > ?
                      ORDER BY c.created_at ASC");
$stmt->execute([$thread_id, $last_comment_id]);
$comments = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($comments);
