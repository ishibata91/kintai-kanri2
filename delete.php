<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php require_once "functions.php"; ?>
    <?php
    session_start();
    $dbh = db_open();
    $id=(int) $_GET['id'];
    //リンクなどからidを取得してidに代入します。
    $sql = 'SELECT userID,name,date FROM dakokudb WHERE id = :id';
    $statement = $dbh->prepare($sql);
    $statement->bindParam(":id", $id, PDO::PARAM_STR);
    $statement->execute();
    $result= $statement->fetch(PDO::FETCH_ASSOC);
    $uID = $result['userID'];
    // $dName = $result['name'];
    $_SESSION['date'] = $result['date'];



    if(hash_equals($_SESSION['uID'], $uID)){
        if(isset($_POST['time'])){
            $sql = "UPDATE dakokudb SET time = :time WHERE id=:id";
            $stmt=$dbh->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":time", $_POST['time'], PDO::PARAM_INT);
            $stmt->execute();

            $sql = 'SELECT time FROM dakokudb WHERE date = :date AND name = :name ORDER BY subject ASC';
            $statement = $dbh->prepare($sql);
            $statement->bindParam(":date", $_SESSION['date'], PDO::PARAM_STR);
            $statement->bindParam(":name", $_SESSION['name'], PDO::PARAM_STR);
            $statement->execute(); 
            while($result = $statement->fetch(PDO::FETCH_ASSOC)){
                $result_array[] = $result;
            }
            if(isset($result_array[1])){
                    $AttendanceTime = strtotime($result_array[0]['time']);
                    $LeavingTime = strtotime($result_array[1]['time']);
                    $diff = round((($LeavingTime - $AttendanceTime)/60)/60, 1);
                    $_SESSION['timeDiff'] = $diff;
                    // Debug($_SESSION['timeDiff']);
                    // Debug($_SESSION['date']);
                    // exit;

                    $sql ="UPDATE kinmujikan SET diff = :diff WHERE name = :name AND date = :date";
                    $stmt = $dbh->prepare($sql);
                    $stmt->bindParam(":diff", $_SESSION['timeDiff'], PDO::PARAM_STR);
                    $stmt->bindParam(":name", $_SESSION['name'], PDO::PARAM_INT);
                    $stmt->bindParam(":date", $_SESSION['date'], PDO::PARAM_INT);
                    $stmt->execute();
            }
            header("Location: form4.php");
        }
        
    // $sql = 'DELETE FROM kinmujikan WHERE name = :name AND date = :date';
    // $statement = $dbh->prepare($sql);
    // $statement->bindParam(":name", $dName, PDO::PARAM_STR);
    // $statement->bindParam(":date", $dDate, PDO::PARAM_STR);
    // $statement->execute();

    }else{
        echo("違うユーザーのデータは削除できません。");
        echo "<br>";
        echo "<a href=form4.php>履歴へ</a>";
        echo "<br>";
        echo "<a href=input.php>入力画面へ</a>";
        echo "<br>";
        echo "<a href=logout.php>ログアウト</a>";
        exit;
    }
    
    ?>
    <form action="" method="post">
    <input type="time" name="time"></input>
    <br>
    <input type="submit" name="submit" value="修正">
    </form>
</body>
</html>