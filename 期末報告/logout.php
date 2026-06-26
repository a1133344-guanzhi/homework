<?php
session_start();
session_unset(); // 清除 Session 變數
session_destroy(); // 銷毀 Session 狀態
header("Location: login.php"); // 導回登入頁
exit;