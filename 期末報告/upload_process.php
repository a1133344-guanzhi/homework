<?php
require_once 'config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 安全防護：沒登入的人不准進來
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES, 'UTF-8');
    $year = intval($_POST['year']);
    
    $month = !empty($_POST['month']) ? intval($_POST['month']) : null;
    $day = !empty($_POST['day']) ? intval($_POST['day']) : null;
    
    $location = htmlspecialchars(trim($_POST['location']), ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars(trim($_POST['content']), ENT_QUOTES, 'UTF-8');
    
    // 接收隱私狀態（預設為 public）
    $status = !empty($_POST['status']) ? $_POST['status'] : 'public'; 
    if (!in_array($status, ['public', 'private'])) {
        $status = 'public';
    }

    $user_id = $_SESSION['user_id'];

    // 後端日期邊界安全檢查
    if ($year < 1) $year = 1;
    if ($month !== null && ($month < 1 || $month > 12)) $month = null;
    if ($day !== null && ($day < 1 || $day > 31)) $day = null;

    // 檢查是否有選擇檔案
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            // 隨機重新命名防止重複
            $new_name = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $upload_dir = 'uploads/';
            
            // 防呆：確保資料夾存在
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) {
                    die("❌ 錯誤：無法建立 uploads/ 資料夾，請檢查伺服器權限！");
                }
            }

            $dest_path = $upload_dir . $new_name;

            // 嘗試移動上傳的檔案
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest_path)) {
                try {
                    // SQL 指令增加寫入 status 欄位
                    $sql = "INSERT INTO memories (user_id, title, year, month, day, location, content, photo_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$user_id, $title, $year, $month, $day, $location, $content, $dest_path, $status]);

                    // 發布成功後導向 timeline.php
                    header("Location: timeline.php?success=1");
                    exit;
                } catch (PDOException $e) {
                    die("❌ 資料庫寫入失敗：" . $e->getMessage() . "<br>請檢查資料庫裡的 memories 資料表欄位是否正確（例如是否漏了 status 欄位）。");
                }
            } else {
                die("❌ 錯誤：檔案上傳失敗 (move_uploaded_file 執行失敗)。請確認伺服器有 uploads/ 的寫入權限。");
            }
        } else {
            die("❌ 錯誤：不支援的檔案格式，只能上傳 JPG, PNG, GIF。");
        }
    } else {
        // 抓出 $_FILES['photo']['error'] 的具體錯誤代碼
        $error_code = isset($_FILES['photo']['error']) ? $_FILES['photo']['error'] : '未知';
        die("❌ 錯誤：檔案接收失敗，PHP 錯誤代碼: " . $error_code . "（如果是代碼 1，代表檔案大小超過了 php.ini 的限制）。");
    }
}
?>