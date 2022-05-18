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
    $sql = 'SELECT userID FROM dakokudb WHERE id = :id';
    $statement = $dbh->prepare($sql);
    $statement->bindParam(":id", $id, PDO::PARAM_STR);
    $statement->execute();
    $userID = $statement->fetch(PDO::FETCH_ASSOC);
    $uID = $userID['userID'];
    if(!empty($uID)){
    if(hash_equals($_SESSION['uID'], $uID)){
    $sql = "DELETE FROM dakokudb WHERE id=:id";
    //SQL、DBテーブル dakokudbの中の、指定されたidを削除しろ
    $stmt=$dbh->prepare($sql);
    //prepareだとユーザーからの入力を受け入れる。DBを書き換えたりしたいとき
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    //これを使うと書き換えができる
    $stmt->execute();
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
    }else{
        echo "エラーが発生しました。";
        echo "<br>";
        echo "<a href=form4.php>履歴へ</a>";
        echo "<br>";
        echo "<a href=input.php>入力画面へ</a>";
        echo "<br>";
        echo "<a href=logout.php>ログアウト</a>";
    }
    header("Location: form4.php");
    ?>
</body>
</html>