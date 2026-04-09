<html>
<head>
    <title>森林野外求生冒險營登入頁面</title>
</head>
<body bgcolor="Beige">
    <center>
        <h1>系統登入</h1>
        <form action="logincheck.php" method="POST">
            帳號:<input type="text" name="uID" required><br/><br/>
            密碼:<input type="password" name="uPwd" required><br/><br/>
            <input type="submit" value="登入">
        </form>

        <?php 
            if(isset($_GET['fail'])){
                echo "<p style='color:red;'>登入失敗，請重新輸入</p>";
            }
        ?>
    </center>
</body>
</html>