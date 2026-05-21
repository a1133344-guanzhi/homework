<?php
//  強迫關閉 PHP 預設的所有輸出緩衝區，確保資料能「一筆一筆即時傳回網頁」
@ini_set('output_buffering', 'off');
@ini_set('zlib.output_compression', '0');
@ob_end_clean(); // 關閉並清除最外層緩衝

ob_start();

// 引入封裝檔
require_once __DIR__ . '/lib/mailer.php';

// 資料庫連線設定 (Database 名稱為 email)
$host = 'localhost';
$db   = 'email'; 
$user = 'root';
$pass = '12345'; //
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
     $pdo = new PDO($dsn, $user, $pass, [
         PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
     ]);
} catch (\PDOException $e) {
     die("<p style='color:red; font-family:sans-serif;'>資料庫連線失敗: " . $e->getMessage() . "</p>");
}

$mode = $_GET['mode'] ?? '';

// 基礎網頁外觀樣式
echo '<style>
    body { font-family: sans-serif; font-size: 13px; color: #333; line-height: 1.5; padding: 15px; margin: 0; }
    .success-msg { color: #137333; background: #e6f4ea; padding: 8px; border-radius: 4px; margin-bottom: 10px; font-weight: 500; }
    .error-msg { color: #c5221f; background: #fce8e6; padding: 8px; border-radius: 4px; margin-bottom: 10px; font-weight: 500; }
    .info-box { background: #e8f0fe; color: #1a73e8; padding: 8px; border-radius: 4px; margin-bottom: 15px; font-weight: bold; }
    .progress-container { width: 100%; background: #e8eaed; height: 12px; border-radius: 6px; margin-bottom: 15px; overflow: hidden; }
    .progress-bar { width: 0%; background: #1a73e8; height: 100%; transition: width 0.2s; }
    .log-item { padding: 4px 0; border-bottom: 1px solid #f1f3f4; color: #5f6368; }
</style>';

//  即時輸出專用函式 (解決瀏覽器死吞資料不吐的問題)
function flush_now($html_content) {
    echo $html_content;
    // 很多現代瀏覽器（特別是 Chrome）需要接收超過 1024 字节才會願意提早渲染畫面
    // 我們在這裡補上隱藏的空白字串，強迫瀏覽器立刻把內容吐到螢幕上
    echo str_repeat(' ', 1024); 
    ob_flush();
    flush();
}

// --- A. 新增 Email 邏輯 ---
if ($mode === 'add_email' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL);
    if ($email) {
        try {
            $stmt = $pdo->prepare("INSERT INTO subscribers (email) VALUES (?)");
            $stmt->execute([$email]);
            echo "<div class='success-msg'> 成功加入：$email</div>";
        } catch (PDOException $e) {
            echo "<div class='error-msg'> 該 Email 已存在或資料庫錯誤。</div>";
        }
    } else {
        echo "<div class='error-msg'> 無效的 Email 格式。</div>";
    }
    exit;
}

// --- B. 寄送郵件邏輯 ---
if ($mode === 'send_email' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject     = $_POST['subject'];
    $content     = $_POST['content'];
    $max_pieces  = (int)$_POST['max_pieces'];   // 前端自訂的「每個帳號隨機筆數上限」
    $max_seconds = (int)$_POST['max_seconds'];  // 前端自訂的「秒數上限」

    if ($max_pieces < 1) $max_pieces = 1;
    if ($max_seconds < 1) $max_seconds = 1;

    // 檢查資料庫實際總筆數
    $count_res = $pdo->query("SELECT COUNT(*) FROM subscribers");
    $total_in_db = $count_res->fetchColumn();

    if ($total_in_db === 0) {
        flush_now("<div class='error-msg'>❌ 資料庫內沒有任何 Email！請先在左側 A 區加入名單。</div>");
        exit;
    }

    // 1. 隨機決定本次的「名單篩選模式」
    $random_mode = rand(0, 1);
    if ($random_mode === 1) {
        // 隨機抽選 1~5 個帳號出來（你可以自行修改上限，這裡範例最多抽選 3 個帳號）
        $target_account_count = rand(1, min(3, $total_in_db));
        $mode_title = "隨機抽樣模式 (本次隨機挑選了 {$target_account_count} 個 Email 帳號發送)";
        $stmt = $pdo->prepare("SELECT email FROM subscribers ORDER BY RAND() LIMIT ?");
        $stmt->bindValue(1, $target_account_count, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $mode_title = "全部寄送模式 (發送給資料庫所有名單，目前共: {$total_in_db} 個帳號)";
        $stmt = $pdo->query("SELECT email FROM subscribers ORDER BY `No` ASC");
    }

    $raw_emails = $stmt->fetchAll(PDO::FETCH_COLUMN);

    //  核心修改 2：建立計畫表，讓每個 Email 帳號都隨機獲得 1 ~ max_pieces 筆郵件
    $send_plan = [];
    foreach ($raw_emails as $email) {
        $pieces_for_this_email = rand(1, $max_pieces); //  為這個帳號抽籤決定要寄幾筆
        for ($i = 1; $i <= $pieces_for_this_email; $i++) {
            $send_plan[] = [
                'email' => $email,
                'current_loop' => $i,
                'total_loop' => $pieces_for_this_email
            ];
        }
    }

    $total_mails_to_send = count($send_plan); // 這是本次總共要發送的信件總筆數

    // 初始化畫面
    flush_now("<div class='info-box'> 系統選擇：{$mode_title}</div>");
    flush_now("<div><strong>發送進度：<span id='p_text'>0%</span> (0 / {$total_mails_to_send})</strong></div>");
    flush_now("<div class='progress-container'><div class='progress-bar' id='p_bar'></div></div>");
    flush_now("<div id='log_box'>");

    // 填入你真實的 Gmail 發信用資訊
    $smtpConfig = [
        'username' => 'a1133344@mail.nuk.edu.tw',
        'password' => 'ztck vlit ajdp aqtq'
    ];

    // 3. 開始逐筆依序寄送
    foreach ($send_plan as $index => $task) {
        $current_index = $index + 1;
        $percent = round(($current_index / $total_mails_to_send) * 100);
        
        $to   = $task['email'];
        $loop = $task['current_loop'];
        $loop_total = $task['total_loop'];

        try {
            // 調用封裝函式取得 PHPMailer 實體
            $mail = build_mailer($smtpConfig);
            
            $mail->addAddress($to);       
            $mail->isHTML(true);          
            // 可以在主旨加上編號，讓收件匣更容易看出「真的寄了隨機很多筆」
            $mail->Subject = "{$subject} (第 {$loop}/{$loop_total} 筆)";
            $mail->Body    = $content;

            $mail->send();
            $status_text = " 已成功寄達";
        } catch (Exception $e) {
            $status_text = "<span style='color:red;'> 寄送失敗 (錯誤: {$mail->ErrorInfo})</span>";
        }

        // 🌟 核心修改 1：利用 JavaScript 與進階快取排除，實時刷新進度與進度條文字
        $ui_update = "<style>#p_bar { width: {$percent}%; }</style>";
        $ui_update .= "<script>document.getElementById('p_text').innerText = '{$percent}% ({$current_index} / {$total_mails_to_send})';</script>";
        $ui_update .= "<div class='log-item'>({$current_index}/{$total_mails_to_send}) {$status_text} ➡️ <b>{$to}</b> [該帳號的第 {$loop}/{$loop_total} 筆]</div>";
        
        // 即時拋出到前端
        flush_now($ui_update);

        // 4. 隨機間隔延時 (最後一筆不需等待)
        if ($current_index < $total_mails_to_send) {
            $random_delay = rand(1, $max_seconds); 
            flush_now("<div class='log-item' style='color:#b06000;'>⏳ 隨機防禦機制：等待 {$random_delay} 秒後發送下一筆...</div>");
            sleep($random_delay); 
        }
    }

    flush_now("<div class='success-msg' style='margin-top:15px;'> 任務完成！本次任務每筆 email 皆已發送隨機數量，共計處理了 {$total_mails_to_send} 封郵件。</div>");
    flush_now("</div>");
    exit;
}
