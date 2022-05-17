<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録</title>
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
    <a href="login.php">ログイン画面へ</a>
    <h1>ユーザー登録</h1>
    <?php
    require_once "functions.php";
    ?>
    <?php error_check_login(); ?>
    <?php if(isset($_SESSION['login'])): ?>
    <a><?php echo $_SESSION['name']; ?>さんは</a>
    <?php endif; ?>
    <form action="signup.php" method = "post">
    <p>
    <label for="username">ユーザー名:</label>
    <input type="text" name='username' value="<?php if(isset($username)){echo $username;} ?>">
    <a class="required">※必須</a>
    </p>
    <p>
    <label for="username">組織名:</label>
    <input type="text" name='org' value="<?php if(isset($org)){echo $org;} ?>">
    <a class="required">※必須</a>
    </p>
    <p>
    <label for="passwordUnconfirmed">パスワード:</label>
    <input type="password" name="passwordUnconfirmed">
    </p>
    <p>
    <label for="password">パスワード確認:</label>
    <input type="password" name="password">
    </p>
    <p>
    <input type="hidden" name='inputCheck' value='isExist'>
    <input type="submit" value="登録">
    </p>
    
    
    </form>   
    
    <?php 
     require_once "functions.php";
     input_check_login();
     if(isset($_POST['inputCheck'])){
     $dbh = db_open();
     $sql = "SELECT * FROM users WHERE username = :username AND org = :org";
     $stmt = $dbh->prepare($sql);
     $stmt->bindParam(":username", $_POST['username'], PDO::PARAM_STR);
     $stmt->bindParam(":org", $_POST['org'], PDO::PARAM_STR);
     $stmt->execute();
     $result = $stmt->fetch(PDO::FETCH_ASSOC);
     if(!empty($_POST['username'])){
        if($_POST['username']==$result['username'] && $_POST['org']==$result['org']){
            die('既にそのユーザーは存在しています。');
        }else{
            passwordConfirmation();
            $password = $_POST['password'];
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            //パスワードハッシュ化
            $dbh = db_open();
            $sql ="INSERT INTO users (id, username, password, org)
            VALUES (NULL, :username, :password, :org)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(":username", $_POST['username'], PDO::PARAM_STR);
            $stmt->bindParam(":password", $password_hashed, PDO::PARAM_STR);
            $stmt->bindParam(":org", $_POST['org'], PDO::PARAM_STR);
            $stmt->execute();
            unset($_POST['inputCheck']);
            if(!empty($_SESSION)){
            unset($_SESSION['inputCheck']);
            }
            header("Location: login.php");           
        }
    }
    }
    ?>
    <a>登録後ログイン画面に遷移します</a>
</body>
</html>