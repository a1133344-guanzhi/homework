<html>
<head>
    <title>學生頁面</title>
</head>
<body>
    <center>
        <?php
        session_start();
        if(isset($_SESSION["login"])){
            if($_SESSION["login"] == "student"){
                echo "<h1>學生個人面板</h1>";
            }else{
                echo "<h1>非法進入網頁!</h1>";
                header("Refresh:1; url=login.php");
            }
        }else{
            echo "<h1>非法進入網頁!</h1>";
            header("Refresh:1; url=login.php");
        }
        ?>
    <a href="logout.php">登出</a>
    </center>
</body>
</html>