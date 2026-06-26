<?php
// 自動判斷是在本機（XAMPP）還是線上（InfinityFree）
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1') {
    $host = 'localhost';
    $dbname = 'memory_museum';
    $user = 'root';
    $pass = '';
} else {
    // ⚠️ 這裡請換成你在 InfinityFree 抄下來的真實資料！
    $host = 'sql211.infinityfree.com'; 
    $dbname = 'if0_42253340_memory_museum'; 
    $user = 'if0_42253340'; 
    $pass = 'hg9C13QBcGjk'; 
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    // 開啟錯誤回報模式，方便除錯
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 全站啟動 Session 紀錄登入狀態
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} catch (PDOException $e) {
    die("資料庫連線失敗: " . $e->getMessage());
}
