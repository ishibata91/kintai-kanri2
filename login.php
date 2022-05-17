<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="stylesheet.css">
    <?php
        if(isset($_POST['username'])){
        $username = $_POST['username'];
        }
        if(isset($_POST['org'])){
            $org = $_POST['org'];
        }
    ?>
</head>
<body>
    <a href="signup.php">登録</a>
    <h1>ログイン</h1>
    <?php
    session_start();
    if(isset($_POST['org'])){
    $_SESSION['org'] = $_POST['org'];
    }
    require_once "functions.php";
    ?>
    <?php error_check_login(); ?>
    <?php if(isset($_SESSION['login'])): ?>
    <a><?php echo $_SESSION['name']; ?>さんは</a>
    <?php endif; ?>
    <form action="login.php" method="post">
        <p>
            <label for="username">ユーザー名:</label>
            <input type="text" name='username' value="<?php if(isset($username)){echo $username;} ?>">
            <a class="required">※必須</a>
        </p>
        <p>
            <label for="org">組織名:</label>
            <input type="text" name='org' value="<?php if(isset($org)){echo $org;} ?>">
            <a class="required">※必須</a>
        </p>
        <p>
            <label for="password">パスワード:</label>
            <input type="password" name='password'>
        </p>
        <input type="hidden" name='inputCheck' value='isExist'>
        <input type="submit" value='送信する'>
    </form>
    <?php input_check_login(); ?>
    <?php if(!empty($_SESSION['login'])): ?>
        <a><?php echo $_SESSION['name']; ?>さん</a>
    <?php endif; ?>
    <?php 
    if(isset($_POST['inputCheck'])){
        $dbh = db_open();
        $sql = "SELECT * FROM users WHERE username = :username AND org = :org";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":username", $_POST['username'], PDO::PARAM_STR);
        $stmt->bindParam(":org", $_POST['org'], PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(isset($_POST['username'])){
        if(!$result){
            //bindparamのせいだと思うけど$_POSTとSQLのデータが一致してないと$resultがfalseになる
            die("ユーザー名か組織名が間違っています。");            
         }
        }
        if(password_verify($_POST['password'], $result['password'])){
            //パスワードがハッシュにマッチするかどうかを調べる一致でtrueしなければfalse中はPOSTのパスとPDOのDBから出てきたパスワードを比べますって感じ
            session_regenerate_id(true);
            //セッションを破壊して新しいセッションを作成する。セッションを乗っ取った後にパスワードの変更を促すような詐欺の対策らしい
            $_SESSION['login'] = true;
            $_SESSION['name'] = $_POST['username'];
            //ログイン状態をこれで保持するらしい
            unset($_POST['inputCheck']);
            if(!empty($_SESSION)){
            unset($_SESSION['inputCheck']);
            }
            header("Location: input.php");
        }else{
            echo 'パスワードが間違っています';
        }
    }
    ?>
</body>
</html>