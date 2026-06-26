<?php
require_once '../config/db.php';

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 💡 安全防護牆：嚴格驗證只有 role === 'admin' 的人有執行權利
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => '權限不足，您並非系統管理員！']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $memory_id = isset($_POST['memory_id']) ? intval($_POST['memory_id']) : 0;
    
    if ($memory_id > 0) {
        try {
            // 1. 提取實體圖片檔案路徑
            $stmt = $pdo->prepare("SELECT photo_path FROM memories WHERE id = ?");
            $stmt->execute([$memory_id]);
            $memory = $stmt->fetch();
            
            if ($memory) {
                // 2. 先刪除該展品下的所有留言（避免形成孤兒髒資料）
                $del_comments = $pdo->prepare("DELETE FROM comments WHERE memory_id = ?");
                $del_comments->execute([$memory_id]);
                
                // 3. 刪除展品本身
                $del_memory = $pdo->prepare("DELETE FROM memories WHERE id = ?");
                $del_memory->execute([$memory_id]);
                
                // 4. 將伺服器資料夾內的實體圖檔砍掉，節省空間
                $file_path = '../' . $memory['photo_path'];
                if (!empty($memory['photo_path']) && file_exists($file_path)) {
                    unlink($file_path);
                }
                
                echo json_encode(['status' => 'success', 'message' => '違規展品及附屬留言已成功徹底清除！']);
            } else {
                echo json_encode(['status' => 'error', 'message' => '找不到該筆展品！']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => '資料庫作業失敗：' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => '展品 ID 格式錯誤！']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '未授權的請求方法！']);
}
?> 