<?php
require_once 'config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 💡 核心安全防護：沒登入的人不能進來；如果身分是管理員(admin)也絕不允許進入上傳頁面
if (!isset($_SESSION['user_id']) || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin')) {
    header("Location: timeline.php");
    exit;
}

include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增回憶展品 - 回憶照片館</title>
    <style>
        :root {
            --bg-color: #FFF8EE; 
            --main-brown: #8B5E3C; 
            --accent-gold: #D4AF37; 
            --text-dark: #2D2926;
        }
        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            font-family: "Microsoft JhengHei", sans-serif;
            margin: 0;
        }
        .upload-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .btn-back {
            display: inline-block;
            background-color: transparent;
            color: var(--main-brown);
            border: 2px solid var(--main-brown);
            padding: 8px 18px;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            margin-bottom: 15px;
        }
        .btn-back:hover {
            background-color: var(--main-brown);
            color: white;
        }

        .upload-section {
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        }
        .upload-section h2 {
            margin-top: 0;
            color: var(--main-brown);
            border-bottom: 2px solid #f3eade;
            padding-bottom: 12px;
            font-size: 1.5em;
        }
        
        .form-group, .date-col { 
            margin-bottom: 20px; 
        }
        
        .form-group label, .date-col label { 
            font-weight: bold; 
            display: block; 
            margin-bottom: 8px; 
            color: var(--main-brown); 
        }
        
        .form-group input[type="text"],
        .form-group select,
        .form-group textarea,
        .date-col input[type="number"] {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95em;
            outline: none;
            box-sizing: border-box;
            transition: border 0.2s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus,
        .date-col input[type="number"]:focus {
            border-color: var(--main-brown);
        }
        
        .date-row {
            display: flex;
            gap: 12px;
        }
        .date-col.year { flex: 2; }
        .date-col.month { flex: 1.5; }
        .date-col.day { flex: 1.5; }
        
        .form-group input[type="file"] {
            padding: 10px 0;
        }
        
        .btn-submit {
            background-color: var(--main-brown);
            color: white;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            box-shadow: 0 4px 12px rgba(139, 94, 60, 0.2);
        }
        .btn-submit:hover {
            background-color: #724c2f;
        }
    </style>
</head>
<body>

    <div class="upload-container">
        <a href="timeline.php" class="btn-back">⬅ 返回時光長卷</a>

        <div class="upload-section">
            <h2>📸 新增回憶展品</h2>
            
            <form action="upload_process.php" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label>回憶主題標題：</label>
                    <input type="text" name="title" placeholder="例如：大一宿營晚會、高中的熱血畢業典禮" required>
                </div>

                <div class="date-row">
                    <div class="date-col year">
                        <label>西元年份 (必填)：</label>
                        <input type="number" name="year" min="1" placeholder="例如：2024" required>
                    </div>
                    <div class="date-col month">
                        <label>月份：</label>
                        <input type="number" name="month" min="1" max="12" placeholder="1-12">
                    </div>
                    <div class="date-col day">
                        <label>日期：</label>
                        <input type="number" name="day" min="1" max="31" placeholder="1-31">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>拍攝地點：</label>
                    <input type="text" name="location" placeholder="例如：淡水漁人碼頭、大安森林公園">
                </div>
                
                <div class="form-group">
                    <label>瀏覽權限設定：</label>
                    <select name="status">
                        <option value="public">🌐 公開 (全站所有人與訪客皆可觀賞)</option>
                        <option value="private">🔒 私密 (僅限自己登入時可在展位頂部看見)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>回憶內文故事：</label>
                    <textarea name="content" rows="5" placeholder="寫下當時深刻、感動或有趣的點滴故事..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>上傳珍貴照片：</label>
                    <input type="file" name="photo" accept="image/*" required>
                </div>
                
                <button type="submit" class="btn-submit">發布到時光長卷</button>
            </form>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>