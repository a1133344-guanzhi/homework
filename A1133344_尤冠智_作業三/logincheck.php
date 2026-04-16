<?php
    session_start();
    
    $uID=$_POST["uID"];
    $uPwd=$_POST["uPwd"];

    $sID="student";
    $sPwd="940918";

    $tID="teacher";
    $tPwd="teacher";

    $aID="admin";
    $aPwd="12345";

    $date=strtotime("+3 days", time());

    if($uID==$sID && $uPwd==$sPwd){
        $_SESSION["login"]="student";
        setcookie("uName", $uID, $date);
        header("Refresh:0; url=student.php");
    }elseif($uID==$tID && $uPwd==$tPwd){
        $_SESSION["login"]= "teacher";
        setcookie("uName", $uID, $date);
        header("Refresh:0; url=teacher.php");
    }elseif($uID==$aID && $uPwd==$aPwd){
        $_SESSION["login"]= "admin";
        setcookie("uName", $uID, $date);
        header("Refresh:0; url=admin.php");
    }else{
        echo "<h2 style='color:red;'>帳號密碼錯誤!</h2>";
        header("Refresh:1; url=login.php");
    }
?>