<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編集</title>
</head>
<body>
<?php
require_once "functions.php";
login_check();
    //------------------------------------------------
    //idを起点として必要な要素をデータからとってくる。
    $dbh = db_open();
    $id=(int) $_GET['id'];
    //リンクなどからidを取得してidに代入します。
    $sql = 'SELECT userID,name,date,subject,time FROM dakokudb WHERE id = :id';
    $statement = $dbh->prepare($sql);
    $statement->bindParam(":id", $id, PDO::PARAM_STR);
    $statement->execute();
    $result= $statement->fetch(PDO::FETCH_ASSOC);
    $updateSubject = $result['subject'];
    $uID = $result['userID'];
    $_SESSION['date'] = $result['date'];
    $beforeTime = $result['time'];
    //----------------------------------------------
        //ログイン時に取得されたユーザーIDと、打刻データのIDから取得されたユーザーIDが一致するか調べる。リンクに適当なIDを入れて編集できなくするため。
        if(hash_equals($_SESSION['uID'], $uID)){
            //削除コード
            if(isset($_POST['deleteFlag'])){                
                    $sql = "DELETE FROM dakokudb WHERE id=:id";
                    $stmt=$dbh->prepare($sql);
                    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                    $stmt->execute();
                    //勤務時間削除
                    $sql ="DELETE FROM kinmujikan WHERE name = :name AND date = :date";
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindParam(":name", $_SESSION['name'], PDO::PARAM_INT);
                    $stmt->bindParam(":date", $_SESSION['date'], PDO::PARAM_INT);
                    $stmt->execute();
                    header("Location: form4.php");
            }
            //下のinputで更新時間がPOSTされた時IDが適合する時間を更新する。
            //やっぱりemptyとissetの違いはissetはbool(false)でもあることになるけどemptyはbool(false)でtrueになること。
            if(!empty($_POST['time'])){
                $sql = "UPDATE dakokudb SET time = :time WHERE id=:id";
                $stmt=$dbh->prepare($sql);
                $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                $stmt->bindParam(":time", $_POST['time'], PDO::PARAM_INT);
                $stmt->execute();
                //勤務時間を計算するコード。上で登録されたデータをもう一回引っ張り出してきて、出勤時間と退勤時間でソートして引き算の整合性をとってる。
                    $sql = 'SELECT time FROM dakokudb WHERE date = :date AND name = :name ORDER BY subject ASC';
                    $statement = $dbh->prepare($sql);
                    $statement->bindParam(":date", $_SESSION['date'], PDO::PARAM_STR);
                    $statement->bindParam(":name", $_SESSION['name'], PDO::PARAM_STR);
                    $statement->execute(); 
                    while($result = $statement->fetch(PDO::FETCH_ASSOC)){
                        $result_array[] = $result;
                    }
                    //上はとってくるだけだけど、こちらは実際に勤務時間を算出してデータを更新するためのコード。もし退勤時間まであれば実行される。
                    if(isset($result_array[1])){
                            $AttendanceTime = strtotime($result_array[0]['time']);
                            $LeavingTime = strtotime($result_array[1]['time']);
                            $diff = round((($LeavingTime - $AttendanceTime)/60)/60, 1);
                            $_SESSION['timeDiff'] = $diff;

                            $sql ="UPDATE kinmujikan SET diff = :diff WHERE name = :name AND date = :date";
                            $stmt = $dbh->prepare($sql);
                            $stmt->bindParam(":diff", $_SESSION['timeDiff'], PDO::PARAM_STR);
                            $stmt->bindParam(":name", $_SESSION['name'], PDO::PARAM_INT);
                            $stmt->bindParam(":date", $_SESSION['date'], PDO::PARAM_INT);
                            $stmt->execute();
                    }
                header("Location: form4.php");
            }else{
                //無を入力できたので追加
                echo '時間を入力してください。';
                echo '<br>';
            }
        //-------------------------------------------------------
    }else{
        //ログイン時のユーザーＩＤとデータのユーザーＩＤが違うとき
        echo("違うユーザーのデータは編集できません。");
        echo "<br>";
        echo "<a href=form4.php>履歴へ</a>";
        echo "<br>";
        echo "<a href=input.php>入力画面へ</a>";
        echo "<br>";
        echo "<a href=logout.php>ログアウト</a>";
        exit;
    }   
?>
    <?php
    echo $_SESSION['name'].'さんの、日付'.$_SESSION['date'].$updateSubject.'を編集します。';
    echo '<br>';
    echo '編集前の時刻:'.$beforeTime;
    ?>
    <form action="" method="post">
    <input type="time" name="time"></input>
    <br>
    <input type="submit" name="submit" value="修正"></input>
    </form>
    <form action="" method="post">
    <input type="hidden" name = "deleteFlag"></input>
    <input type="submit" name="submit" value="削除" onclick="return confirm('本当に削除しますか？');"></input>
</body>
</html>