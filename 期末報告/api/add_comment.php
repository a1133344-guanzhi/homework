<?php
require_once '../config/db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $memory_id = intval($_POST['memory_id']);
    $comment_text = htmlspecialchars(trim($_POST['comment_text']), ENT_QUOTES, 'UTF-8');

    // 💡 核心邏輯：判斷目前是否有會員登入
    if (isset($_SESSION['user_id'])) {
        // 如果登入了，直接抓 Session 裡的用戶名，不給訪客篡改的機會
        $visitor_name = $_SESSION['username'] . " (主人)";
    } else {
        // 如果是訪客，才抓前端傳過來的暱稱
        $visitor_name = !empty(trim($_POST['visitor_name'])) ? htmlspecialchars(trim($_POST['visitor_name']), ENT_QUOTES, 'UTF-8') : '訪客';
    }

    if (!empty($comment_text) && $memory_id > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO comments (memory_id, visitor_name, comment_text) VALUES (?, ?, ?)");
            $result = $stmt->execute([$memory_id, $visitor_name, $comment_text]);

            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'visitor_name' => $visitor_name,
                    'comment_text' => $comment_text
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => '寫入失敗']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => '留言內容不可為空']);
    }
}
