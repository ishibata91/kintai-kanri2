<?php
require_once "functions.php";
login_check();
//確認ダイアログを表示するための変数定義
if(isset($_SESSION['date'])){
    $date=$_SESSION['date'];
    $subject=$_SESSION['subject'];
    $time=$_SESSION['time'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>確認です</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <?php 
    //出退勤が同じだと打刻できないようにする
    $dbh = db_open();
    //出退勤と名前、日付が一緒のデータが出てきたら中止
    $sql = 'SELECT subject FROM dakokudb WHERE date = :date AND subject = :subject AND name = :name';
    $statement = $dbh->prepare($sql);
    $statement->bindParam(":date", $_SESSION['date'], PDO::PARAM_STR);
    $statement->bindParam(":subject", $_SESSION['subject'], PDO::PARAM_STR);
    $statement->bindParam(":name", $_SESSION['name'], PDO::PARAM_STR);
    $statement->execute(); 
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if(!empty($result)){
    if($_SESSION['subject'] == $result['subject']){
        echo "既にこの打刻は完了しています。";
        unset($_SESSION['date']);
        unset($_SESSION['subject']);
        unset($_SESSION['time']);
        echo "<p><a href='input.php?action=edit'>入力画面へ戻る</a></p>";
        exit;
    }
    }
    //退勤時間から入力しようとしたら防止
    $dbh = db_open();
    //名前と日付が同じデータから出退勤と時間を引っ張り出す。
    //中身が空で、退勤を入力しようとしてるなら防止
    $sql = 'SELECT subject FROM dakokudb WHERE date = :date AND name = :name';
    $statement = $dbh->prepare($sql);
    $statement->bindParam(":date", $_SESSION['date'], PDO::PARAM_STR);
    $statement->bindParam(":name", $_SESSION['name'], PDO::PARAM_STR);
    $statement->execute(); 
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if(empty($result['subject'])){
        if($_SESSION['subject'] == "退勤時間"){
            echo "まずは出勤時間から入力してください。";
            unset($_SESSION['date']);
            unset($_SESSION['subject']);
            unset($_SESSION['time']);
            echo "<p><a href='input.php?action=edit'>入力画面へ戻る</a></p>";
            exit;
        }
    }
    ?>
    <p>これでよろしいですか？</p>
    <form action="" method="post">
            <h3 class="Description">名前</h3><?php echo $_SESSION['name'];?>
            <br>
            <h3 class="Description">日付</h3><?php echo $date; ?>
            <h3 class="Description"><?php echo $subject; ?></h3><?php echo ($time); ?>
            <br>
            <input type="hidden" name= "Check"></input>
            <input type="submit" name= "submit" value="送信する" >  
    </form>
    
    <?php 
    //こうしないとform3にアクセスした後履歴に飛んだら必要なSESSIONがないのにファーストフラグがついちゃってエラー吐いちゃった
    if(isset($_POST['Check'])){
    $_SESSION['isFirst'] = true;
    unset($_SESSION['Check']);
    header("Location: form4.php");
    }
     ?>
    <p><a href="input.php?action=edit">入力画面へ戻る</a></p>
    <!-- indexになんかGETメソッドでデータ送ってるらしい。 -->
</body>
</html>


            