<?php
require_once '../config/db.php';

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // 基礎 SQL：公開照片，或目前登入者自己的私密照片
    $sql = "SELECT m.*, u.username FROM memories m 
            INNER JOIN users u ON m.user_id = u.id 
            WHERE (m.status = 'public' OR (m.status = 'private' AND m.user_id = :current_user_id))";
    
    if ($search !== '') {
        $sql .= " AND (m.title LIKE :search 
                    OR m.year LIKE :search_year 
                    OR m.location LIKE :search_loc 
                    OR m.content LIKE :search_content)";
    }
    
    // 排序：當前登入者優先，其餘按用戶名及年份遞增
    $sql .= " ORDER BY (CASE WHEN m.user_id = :order_user_id THEN 0 ELSE 1 END) ASC, u.username ASC, m.year ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':current_user_id', $current_user_id, PDO::PARAM_INT);
    $stmt->bindValue(':order_user_id', $current_user_id, PDO::PARAM_INT);
    
    if ($search !== '') {
        $stmt->bindValue(':search', '%'.$search.'%', PDO::PARAM_STR);
        $stmt->bindValue(':search_year', '%'.$search.'%', PDO::PARAM_STR);
        $stmt->bindValue(':search_loc', '%'.$search.'%', PDO::PARAM_STR);
        $stmt->bindValue(':search_content', '%'.$search.'%', PDO::PARAM_STR);
    }
    
    $stmt->execute();
    $memories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 撈取留言
    foreach ($memories as $key => $memory) {
        $m_id = $memory['id'];
        $c_stmt = $pdo->prepare("SELECT visitor_name, comment_text FROM comments WHERE memory_id = ? ORDER BY id ASC");
        $c_stmt->execute([$m_id]);
        $memories[$key]['comments'] = $c_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode(['status' => 'success', 'data' => $memories]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}