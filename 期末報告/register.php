<?php
require_once 'config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$is_success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            $message = "此帳號已被註冊，請換一個名字！";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'owner')");
            if ($insert->execute([$username, $password_hash])) {
                $message = "🎉 註冊成功！";
                $is_success = true;
            }
        }
    } else {
        $message = "請填寫完整欄位！";
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>申請策展人 - 時光長卷</title>
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
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .auth-card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(139, 94, 60, 0.08);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
            text-align: center;
            position: relative;
        }
        .auth-card h2 {
            color: var(--main-brown);
            margin-top: 0;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        .auth-card p.subtitle {
            color: #888;
            font-size: 0.85em;
            margin-bottom: 30px;
        }
        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            color: var(--main-brown);
            font-size: 0.9em;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1em;
            transition: all 0.3s;
        }
        .form-group input:focus {
            border-color: var(--main-brown);
            outline: none;
            box-shadow: 0 0 5px rgba(139, 94, 60, 0.2);
        }
        .btn-submit {
            background: var(--main-brown);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            width: 100%;
            cursor: pointer;
            font-weight: bold;
            font-size: 1em;
            margin-top: 10px;
            transition: background 0.3s;
        }
        .btn-submit:hover {
            background: #734d31;
        }
        .message-box {
            padding: 10px;
            border-radius: 6px;
            font-size: 0.85em;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .error-msg { background: #FFF0F0; color: #D9534F; }
        .success-msg { background: #EFFFF0; color: #2B8A3E; }
        .switch-link {
            margin-top: 25px;
            font-size: 0.9em;
            color: #666;
        }
        .switch-link a {
            color: var(--main-brown);
            text-decoration: none;
            font-weight: bold;
        }
        .switch-link a:hover {
            text-decoration: underline;
        }
        .back-home {
            position: absolute;
            top: -50px;
            left: 0;
            right: 0;
            text-align: center;
        }
        .back-home a {
            color: var(--main-brown);
            text-decoration: none;
            font-size: 0.95em;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <div class="back-home"><a href="index.php">⬅ 返回時光長卷首頁</a></div>
        <h2>✍️ 註冊新展位</h2>
        <p class="subtitle">建立專屬帳號，開啟您的數位時光策展旅程</p>

        <?php if(!empty($message)): ?>
            <div class="message-box <?php echo $is_success ? 'success-msg' : 'error-msg'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if(!$is_success): ?>
        <form method="POST">
            <div class="form-group">
                <label>自訂策展帳號：</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>設定安全密碼：</label>
                <input type="password" name="password" placeholder="請設定您的密碼" required>
            </div>
            <button type="submit" class="btn-submit">建立帳號</button>
        </form>
        <?php else: ?>
            <div style="margin-top: 20px;">
                <a href="login.php" class="btn-submit" style="display:block; text-decoration:none; line-height:24px;">前往登入網頁</a>
            </div>
        <?php endif; ?>

        <div class="switch-link">
            已經有策展人帳號了？ <a href="login.php">直接登入</a>
        </div>
    </div>

</body>
</html>