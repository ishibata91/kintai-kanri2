<?php
require_once "functions.php";
login_check();
require_once "header.php";
$errors=array();
if(isset($_POST['submit'])){
//issetは変数が設定されているかどうかを判定する。最初は$_POSTが出力されてないのでfalse
//次にsubmitを押下した時は$_POSTがあるのでtrueになる
$date=$_POST['date'];
$subject=$_POST['subject'];
$time=$_POST['time'];

str2html($date);
str2html($subject);
str2html($time);
//htmlspecialchars 関数 は、&、<、> を '&amp;' 、'& lt;' 、 '&gt;' に 変換 する 関数 です。
//多分フォームにプログラム書かれて攻撃されないようにするのかな？多分
 


if($date===""){
    $errors['date']="日付が入力されていません。";
}

if($time===""){
    $errors['time']="時間が入力されていません。";
 }
 if(count($errors)===0){
     //もしエラーの数が0ならば
     $_SESSION['date']=$date;
     $_SESSION['subject']=$subject;
     $_SESSION['time']=$time;
    //$_SESSIONでセッションに保存されて他ページでも取得可能になる
    //$_POSTとの違い:$_POSTは他に送るものである。$_SESSIONはサーバー内に保存するためどのページからでもアクセスできるようになる
     header('Location:form3.php');
     //header関数は、htmlのheadに情報を追加できるっていう関数　Locationを使えば情報を受け取った瞬間遷移する
     
 }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理2</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <h1 class="title">勤怠管理2</h1>
<?php 
echo "<ul>";
foreach($errors as $value){
    //$errorsの配列を上から取り出す。まずnameである。空の時、その値は名前が～になるので、その値は$valueに代入される。以下繰り返し
    echo "<li>";
    echo $value;
    echo "</li>";
}

echo "</ul>";
?>
    <form action="input.php" method="post">


                <p class="Description"><?php echo $_SESSION['org']; ?>の<?php echo $_SESSION['name']; ?>さんの打刻</p>
                <input type="hidden" name="name" value="<?php if(isset($name)){echo $name;} ?>" class="smallForms">



                <?php 
                //現在の時間を取得します
                date_default_timezone_set ('Asia/Tokyo');
                $today = date("Y-m-d");
                $now = date('H:i');
                ?>
                <p class="Description">日付を入力してください<span class="required">※必須</span></p>
                    <input type="date" name="date" value="<?php echo $today; ?>" class="smallForms" max="<?php echo $today; ?>">
                    <!-- 現在の時間を最初から設定されているように -->
                    <p class="Description">時間を入力してください<span class="required">※必須</span></p>
                    <input type="time" name="time" value="<?php echo $now;?>" class="smallForms" >
                    <br>
                    <p class="Description">選択してください<span class="required">※必須</span></p>
                <select name="subject" class="smallForms">
                    <option value="出勤時間" <?php if($now <= strtotime('12:00')){ echo"selected";} ?>>出勤時間</option>
                    <!-- 午前なら出勤時間 -->
                    <option value="退勤時間" <?php if($now > strtotime('12:00')){ echo"selected";} ?>>退勤時間</option>
                    <!-- 午後なら出勤時間 -->
                    </select>
                    <br>
                    <input type="submit" name="submit" value="確認画面へ">
    </form>
    <p><a href="form4.php">履歴表示</a></p>
    
</body>
</html>
