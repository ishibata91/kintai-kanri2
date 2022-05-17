<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
            <a><?php echo $_SESSION['org']; ?>の</a>
            <a><?php echo $_SESSION['name']; ?>さん、こんにちは！</a>
            <a href="logout.php">ログアウト</a>
</body>
</html>