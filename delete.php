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
    $id=(int) $_GET['id'];
    //リンクなどからidを取得してidに代入します。
    $dbh = db_open();
    //関数呼び出し
    $sql = "DELETE FROM dakokudb WHERE id=:id";
    //SQL、DBテーブル dakokudbの中の、指定されたidを削除しろ
    $stmt=$dbh->prepare($sql);
    //prepareだとユーザーからの入力を受け入れる。DBを書き換えたりしたいとき
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    //これを使うと書き換えができる
    $stmt->execute();
    header("Location: form4.php");
    ?>
</body>
</html>