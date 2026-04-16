<?php
    setcookie("uName", '', time()-1);
    header("Refresh:0; url=login.php");
?>