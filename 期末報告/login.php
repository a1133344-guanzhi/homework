<?php
require_once 'config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 💡 修正：如果已經登入了，直接導向時光長卷頁面
if (isset($_SESSION['user_id'])) {
    header("Location: timeline.php");
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // 💡 修正：登入成功後直接導向 timeline.php
            header("Location: timeline.php");
            exit;
        } else {
            $message = "帳號或密碼輸入錯誤！";
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
    <title>登入展覽館 - 回憶照片館</title>
    <style>
        :root {
            --bg-color: #FFF8EE;
            --main-brown: #8B5E3C;
            --text-dark: #2D2926;
        }
        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            font-family: "Microsoft JhengHei", sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .auth-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(139, 94, 60, 0.08);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
            position: relative;
        }
        h2 {
            margin-top: 0;
            color: var(--main-brown);
            text-align: center;
        }
        .subtitle {
            text-align: center;
            color: #777;
            font-size: 0.9em;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: var(--main-brown);
        }
        .form-group input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            box-sizing: border-box;
        }
        .form-group input:focus {
            border-color: var(--main-brown);
        }
        .btn-submit {
            background: var(--main-brown);
            color: white;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 1em;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background: #724c2f;
        }
        .message-box {
            background: #fde8e8;
            color: #e53e3e;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9em;
            text-align: center;
        }
        .switch-link {
            text-align: center;
            margin-top: 20px;
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
            text-align: center;
            margin-bottom: 20px;
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
        <div class="back-home"><a href="index.php">⬅ 返回博物館首頁</a></div>
        <h2>🏛️ 登入展覽館</h2>
        <p class="subtitle">歡迎回來策展人，請輸入您的帳號密碼</p>

        <?php if(!empty($message)): ?>
            <div class="message-box"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>帳號名稱：</label>
                <input type="text" name="username" placeholder="請輸入您的帳號" required>
            </div>
            <div class="form-group">
                <label>密碼：</label>
                <input type="password" name="password" placeholder="請輸入您的密碼" required>
            </div>
            <button type="submit" class="btn-submit">驗證並登入</button>
        </form>

        <div class="switch-link">
            還沒有展位嗎？ <a href="register.php">註冊新策展人帳號</a>
        </div>
    </div>

</body>
</html>