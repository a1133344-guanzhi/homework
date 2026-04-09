<html>
<head>
    <title>報名結果確認 - 森林野外求生冒險營</title>
</head>
<body bgcolor="Beige">
    <center>
        <h1>報名資料接收成功!</h1>
        <p>恭喜你邁向冒險者的第一步!以下是您的報名資訊:</p>
        <?php
            $name = $_POST["nName"];
            $gender = isset($_POST["mGender"]) ? $_POST["mGender"] : "未填寫";
            $city = isset($_POST["nCity"]) ? $_POST["nCity"] : "未選擇";
            $interests = "";
            if (isset($_POST["mInterest"])){
                $interests = implode("、", $_POST["mInterest"]);
            }else{
                $interests = "無感興趣的項目";
            }
            $email = $_POST["mEmail"];

            echo "<b>姓名： </b> $name<br/>";
            echo "<b>性別： </b> $gender<br/>";
            echo "<b>聯絡 Email: </b> $email<br/>";
            echo "<b>出發城市: </b> $city<br/>";
            echo "<b>感興趣的技能: </b> $interests";
        ?>
    </center>
</body>
</html>