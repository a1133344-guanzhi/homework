<?php
    $fID="celeste";
    $fPwd="940918";

    if(isset($_POST["uID"]) && isset($_POST["uPwd"])){
        $uID=$_POST["uID"];
        $uPwd=$_POST["uPwd"];

        if($uID == $fID && $uPwd == $fPwd){
            header("Location: form.php");
        }else{
            echo "<h2 style='color:red;'>帳號密碼錯誤!</h2>";
            echo "<p>系統將在 2 秒後跳回登入頁面...</p>";
            header("Refresh:2;url= login.php?fail=1");
        }
    }
?>