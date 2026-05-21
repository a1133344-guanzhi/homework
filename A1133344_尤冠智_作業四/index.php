<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mail - 垃圾郵件發送系統</title>
    <style>
        :root {
            --primary-color: #1a73e8;
            --bg-color: #f6f8fc;
            --text-color: #202124;
            --border-color: #dadce0;
        }
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
        }
        .sidebar {
            width: 250px;
            padding: 16px;
            box-sizing: border-box;
            background-color: var(--bg-color);
        }
        .compose-btn {
            background-color: #c2e7ff;
            color: #001d35;
            border: none;
            padding: 16px 24px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            width: 100%;
            margin-bottom: 20px;
        }
        .sidebar-item {
            padding: 8px 24px;
            border-radius: 0 16px 16px 0;
            font-size: 14px;
            color: #444746;
            font-weight: bold;
            background-color: #e8f0fe;
            margin-left: -16px;
        }
        .main-content {
            flex: 1;
            background-color: #ffffff;
            margin: 16px 16px 16px 0;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        .header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-color);
            font-size: 18px;
            font-weight: 400;
        }
        .container-box {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        .form-section {
            flex: 1;
            padding: 24px;
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
        }
        .status-section {
            width: 400px;
            background-color: #f9fbfd;
            display: flex;
            flex-direction: column;
        }
        .status-header {
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 500;
            background-color: #f1f3f4;
            border-bottom: 1px solid var(--border-color);
        }
        .status-iframe {
            width: 100%;
            flex: 1;
            border: none;
            background: transparent;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #444746;
            margin-bottom: 6px;
        }
        input[type="text"], input[type="email"], input[type="number"], textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="number"]:focus, textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(26,115,232,0.1);
        }
        .flex-row {
            display: flex;
            gap: 12px;
        }
        .submit-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 100px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .submit-btn:hover {
            background-color: #1557b0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .secondary-btn {
            background-color: transparent;
            color: var(--primary-color);
            border: 1px solid var(--border-color);
            padding: 8px 16px;
            border-radius: 100px;
            font-size: 13px;
            cursor: pointer;
        }
        .secondary-btn:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <button class="compose-btn"> 撰寫郵件</button>
        <div class="sidebar-item"> 收件匣設定</div>
    </div>

    <div class="main-content">
        <div class="header">
            <span> 垃圾郵件發送系統</span>
        </div>

        <div class="container-box">
            <div class="form-section">
                <form action="action.php?mode=add_email" method="POST" target="status_frame" style="margin-bottom: 35px;">
                    <h3 style="font-size: 16px; margin-top: 0;">A. 目標 Email</h3>
                    <div class="form-group">
                        <label>新增目標 Email</label>
                        <div class="flex-row">
                            <input type="email" name="user_email" required placeholder="user@example.com">
                            <button type="submit" class="secondary-btn" style="white-space: nowrap;">加入</button>
                        </div>
                    </div>
                </form>

                <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 24px 0;">

                <form action="action.php?mode=send_email" method="POST" target="status_frame">
                    <h3 style="font-size: 16px; margin-top: 0;">B. 群發郵件設定</h3>
                    
                    <div class="form-group">
                        <label>主旨</label>
                        <input type="text" name="subject" required>
                    </div>

                    <div class="form-group">
                        <label>內容</label>
                        <textarea name="content" rows="6" required placeholder="請輸入郵件內文..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>隨機件數上限 (若抽中隨機模式，最多寄幾件)</label>
                        <div class="flex-row" style="align-items: center;">
                            <span style="font-size:14px; color:#555;">1 到</span>
                            <input type="number" name="max_pieces" min="1" value="3" style="width: 120px;" required>
                            <span style="font-size:14px; color:#555;">件</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>隨機間隔秒數上限 (每封信最大延遲時間)</label>
                        <div class="flex-row" style="align-items: center;">
                            <span style="font-size:14px; color:#555;">1 到</span>
                            <input type="number" name="max_seconds" min="1" value="3" style="width: 120px;" required>
                            <span style="font-size:14px; color:#555;">秒</span>
                        </div>
                    </div>

                    <p style="font-size: 12px; color: #666; background: #f1f3f4; padding: 10px; border-radius: 6px;">
                        系統提示：每次按下傳送，發送模式（全部/隨機）仍由後端隨機抽籤決定。
                    </p>

                    <button type="submit" class="submit-btn">傳送</button>
                </form>
            </div>

            <div class="status-section">
                <div class="status-header">發送狀態與日誌</div>
                <iframe name="status_frame" class="status-iframe" src="about:blank"></iframe>
            </div>
        </div>
    </div>

</body>
</html>