<?php 
//XSS対策です
function str2html(string $string) :string{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
//データベース開く時の関数です　このユーザーで、このオプションで、データベースをひらきますって感じ
function db_open(){
    $user = "phpuser";
    $password = "aiueo";
    $opt = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
    ];
    $dbh = new PDO('mysql:host=localhost;dbname=dakoku',$user,$password,$opt);
    return $dbh;
}
//ログイン済みの場合ページ読込を任意のタイミングで破壊できます
function error_check_login(){
    if(!isset($_SESSION)){
        session_start();
    }
    if(isset($_SESSION['login'])){
        echo "ログイン済みです<br>";
        echo "<a href=form4.php>履歴へ</a>";
        echo "<br>";
        echo "<a href=input.php>入力画面へ</a>";
        echo "<br>";
        echo "<a href=logout.php>ログアウト</a>";
        die;
    }
}
//ログインしてない時にページの読込を破壊します
function login_check(){
if(!isset($_SESSION)){
    session_start();
}
if(empty($_SESSION['login'])){
    die("このページアクセスするには<a href='login.php'>ログイン</a>が必要です。");
}else{
    echo "<!--ログイン中-->";
}
}
//パスワードやユーザー名が入力されていなかったり、文字数が制限を超えたらページ読込を破壊します。
        //\Aは文字列の先頭、\zは文字列の最後。[:^cntrl:]は制御文字（改行など）以外の全ての文字を表す。specialcharsの逆。[:^cntrl:]は[]の中でしか存在できない/uはUTF-8ということ。!preg_match→マッチしないなら～
function input_check_login(){
    if(!empty($_POST['inputCheck'])){
     if((empty($_POST['username']))){
        $noUsername = "ユーザー名が入力されていません";
    }
    if((empty($_POST['password']))){
        $noPassword = "パスワードが入力されていません。";
    }
    if(empty($_POST['org'])){
        $noOrg = "組織名が入力されていません。";
    }
    if(!empty($noUsername)){
        echo '<a>'.$noUsername.'</a>';
    }
    if(!empty($noPassword)){
        echo '<p>' .$noPassword. '</p>';
    }
    if(!empty($noOrg)){
        echo '<p>' .$noOrg. '</p>';
    }
    if(!empty($noUsername)||!empty($noPassword)||!empty($noOrg)){
        exit;
    }
    //どれかでdieを出すと他の情報が消えるのでこんなことをした
    //echoするだけだとexitする時の判定が作れなかったため変数代入
    if(!preg_match('/\A[[:^cntrl:]]{1,16}\z/u',$_POST['username'])){
        die("ユーザー名は16文字までです。");
        }
    if(!preg_match('/\A[[:^cntrl:]]{1,16}\z/u',$_POST['password'])){
         die("パスワードは16文字までです。");
        }
    }
}
//パスワードと確認用パスワードのインプットが一致してるか判断します。一致しなければデータベースへの登録を行わない位置に置きます。
function passwordConfirmation(){
    if(isset($_POST['password'])){
        if($_POST['passwordUnconfirmed']!==$_POST['password']){
            die("パスワードが一致していません。");
        }
    }
}
//できん 代入演算子でramdom_bytesを変数代入しているだけで、値を代入してるわけじゃないって感じで読み直すたびにランダムな値吐いてるのかも
function antiCSRF(){
    if(!isset($_SESSION)){
        session_start();
    }
    if(empty($_POST['token'])){
        echo 'エラーが発生しました';
        exit;
    }
    if(!(hash_equals($_SESSION['token'],$_POST['token']))){
        echo 'エラーが発生しました';
        exit;
    }
}
//POSTとセッション見るだけ 単一可
function Debug($What_you_seek){
    echo '<br>';
    echo '-------------------------------------------------------------';
    echo '<br>';
    var_dump($What_you_seek);
    echo '<br>';
    echo '-------------------------------------------------------------';
}
//配列が長すぎて読みにくいので作った。注：配列以外不可
function DebugArray($What_you_seek){
    echo '<br>';
        echo '-------------------------------------------------------------';
    foreach($What_you_seek as $gomi){
        echo '<br>';
        var_dump($gomi);
        echo '<br>';
         }
         echo
        '-------------------------------------------------------------';
}
?>
