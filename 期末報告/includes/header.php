<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav style="background: rgba(255, 255, 255, 0.9); padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(139, 94, 60, 0.1); box-shadow: 0 2px 10px rgba(0,0,0,0.02); font-family: 'Microsoft JhengHei', sans-serif;">
    <a href="index.php" style="font-size: 1.3em; font-weight: bold; color: #8B5E3C; text-decoration: none;">🏛️ 回憶照片館</a>
    <div class="nav-right" style="font-size: 0.95em; display: flex; align-items: center;">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="upload.php" style="margin-right: 20px; text-decoration: none; color: #8B5E3C; font-weight: bold;">📸 我要策展</a>
            <span style="color: #2D2926;">歡迎回來，<strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <span style="background: #E53E3E; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.8em; margin-left: 6px; font-weight: bold;">管理者</span>
            <?php endif; ?>
            <a href="logout.php" style="margin-left: 20px; color: #E53E3E; text-decoration: none; font-weight: bold;">登出</a>
        <?php else: ?>
            <span style="color: #666; margin-right: 20px;">目前身份：<strong>訪客</strong></span>
            <a href="login.php" style="margin-right: 15px; text-decoration: none; color: #8B5E3C; font-weight: bold;">🔑 登入展覽館</a>
            <a href="register.php" style="text-decoration: none; color: white; background: #8B5E3C; padding: 6px 14px; border-radius: 20px; font-weight: bold; font-size: 0.9em; transition: background 0.2s;" onmouseover="this.style.background='#724c2f'" onmouseout="this.style.background='#8B5E3C'">✍️ 註冊新展位</a>
        <?php endif; ?>
    </div>
</nav>
