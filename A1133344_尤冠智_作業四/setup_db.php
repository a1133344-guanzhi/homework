<?php
header('Content-Type: text/html; charset=utf-8');
echo "<style>body { font-family: sans-serif; line-height: 1.5; padding: 20px; color: #333; } .success { color: green; font-weight: bold; } .error { color: red; font-weight: bold; }</style>";
echo "<h2> 資料庫自動建置系統</h2>";

// 1. 基礎連線設定 (先連線到 MySQL 本體)
$host = 'localhost';
$user = 'root';
$pass = '12345';
$charset = 'utf8mb4';

try {
    // 建立不指定特定資料庫的 PDO 連線
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo " 成功連線至 MySQL 伺服器...<br>";

    // 2. 建立資料庫 (如果不存在的話)
    $db_name = 'email';
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<span class='success'> 資料庫 `$db_name` 建置或確認成功！</span><br>";

    // 3. 切換並連線至該資料庫
    $pdo->exec("USE `$db_name`");

    // 4. 建立 subscribers 資料表
    // 欄位包含：No (主鍵、自動遞增)、email (唯一值，不可重複)
    $create_table_sql = "
        CREATE TABLE IF NOT EXISTS `subscribers` (
            `No` INT(11) NOT NULL AUTO_INCREMENT,
            `email` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`No`),
            UNIQUE KEY `unique_email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($create_table_sql);
    echo "<span class='success'> 資料表 `subscribers` 建置或確認成功！</span><br>";

    // 5. 自動預填測試資料 (防呆機制：如果裡面沒資料，就塞入幾筆)
    $check_stmt = $pdo->query("SELECT COUNT(*) FROM `subscribers`");
    $count = $check_stmt->fetchColumn();

    if ($count == 0) {
        $sample_emails = [
            'test@gmail.com',
            'a1133344@mail.nuk.edu.tw'
        ];

        $insert_stmt = $pdo->prepare("INSERT INTO `subscribers` (`email`) VALUES (?)");
        foreach ($sample_emails as $email) {
            $insert_stmt->execute([$email]);
            echo " 已預先載入測試名單：$email<br>";
        }
        echo "<span class='success'> 測試資料填入完成！</span><br>";
    } else {
        echo "ℹ 資料庫內已有 $count 筆名單，跳過預填資料步驟。<br>";
    }

    echo "<br><p class='success'> 全部設定大功告成！現在你可以正常前往首頁操作群發系統了。</p>";
    echo "<a href='index.php' style='display:inline-block; background:#1a73e8; color:white; padding:8px 16px; border-radius:4px; text-decoration:none; margin-top:10px;'>點我前往首頁 (index.php)</a>";

} catch (PDOException $e) {
    echo "<br><div class='error'> 發生錯誤，建置失敗：</div>" . $e->getMessage();
    echo "<br><br>💡 提示：請檢查你的 `setup_db.php` 第 8-10 行的 MySQL 帳號密碼是否正確。";
}