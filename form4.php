<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>履歴です</title>
    <script src="https://code.jquery.com/jquery-1.12.4.js" integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU=" crossorigin="anonymous"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script src="script.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
    <?php
    require_once "functions.php";
    login_check();
    $dbh = db_open();
    //form3にアクセスせずに履歴にアクセスするようなことがあったら、これが無いと下のifでエラーが出る。form3でファーストフラグを定義しているため
    if(!isset($_SESSION['isFirst'])){
        $_SESSION['isFirst'] = false;
    }
    //↓は今まで受けた変数を書き込みます
    if($_SESSION['isFirst']){
        $sql ="INSERT INTO dakokudb (name,date,subject,time,id,org,userID)
        VALUES (:name, :date, :subject, :time, NULL, :org, :userID)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":name", $_SESSION['name'], PDO::PARAM_STR);
        $stmt->bindParam(":date", $_SESSION['date'], PDO::PARAM_STR);
        $stmt->bindParam(":subject", $_SESSION['subject'], PDO::PARAM_STR);
        $stmt->bindParam(":time", $_SESSION['time'], PDO::PARAM_STR);
        $stmt->bindParam(":org", $_SESSION['org'], PDO::PARAM_STR);
        $stmt->bindParam(":userID", $_SESSION['uID'], PDO::PARAM_STR);
        $stmt->execute();
       //上で書き込まれたデータを読んで出勤時間と退勤時間を引っ張りだす。
        $sql = 'SELECT time FROM dakokudb WHERE date = :date AND name = :name ORDER BY subject ASC';
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":date", $_SESSION['date'], PDO::PARAM_STR);
        $statement->bindParam(":name", $_SESSION['name'], PDO::PARAM_STR);
        $statement->execute(); 
        while($result = $statement->fetch(PDO::FETCH_ASSOC)){
            $diff_array[] = $result;
        }
        //もし両方あったとしたら、その差を算出して記録する。
        if(isset($diff_array[1])){
                $AttendanceTime = strtotime($diff_array[0]['time']);
                $LeavingTime = strtotime($diff_array[1]['time']);
                $diff = round((($LeavingTime - $AttendanceTime)/60)/60, 1);
                $_SESSION['timeDiff'] = $diff;

                $sql ="INSERT INTO kinmujikan (id,name,date,userID,diff)VALUES(NULL, :name, :date, :userID, :diff)";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(":name", $_SESSION['name'], PDO::PARAM_STR);
                $stmt->bindParam(":date", $_SESSION['date'], PDO::PARAM_STR);
                $stmt->bindParam(":userID", $_SESSION['uID'], PDO::PARAM_STR);
                $stmt->bindParam(":diff", $_SESSION['timeDiff'], PDO::PARAM_STR);
                $stmt->execute();
                $_SESSION['isFirst'] = false;
        }
        //ファーストフラグがあるので保持してても問題ないけど一応unset
        $_SESSION['isFirst'] = false;
        unset($_SESSION['time']);
        unset($_SESSION['subject']);
        unset($_SESSION['date']);
    }
    ?>
</head>
<body>
<?php require_once "header.php" ?>
    <h1>履歴</h1>
    <a href="input.php">入力画面へ</a>
        <?php require_once("Calender.php");?>
          <!-- ライブラリ使用してるのでクラス名とかは謎。calender.phpで定義したものを出力するのが中心 -->
            <div class="container">
                <h3 class="mb-5"><a href="?ym=<?php echo $prev; ?>">&lt;</a> <?php echo $html_title; ?> <a href="?ym=<?php echo $next; ?>">&gt;</a></h3>
                <table class="table table-bordered">
                    <tr>
                        <th>日</th>
                        <th>月</th>
                        <th>火</th>
                        <th>水</th>
                        <th>木</th>
                        <th>金</th>
                        <th>土</th>
                    </tr>
                    <?php
                        foreach ($weeks as $week) {
                            echo $week;
                        }
                    ?>
                </table>
            </div>
        <?php //CSV書き込み処理
        $dbh = db_open();
        $sql = 'SELECT * FROM dakokudb WHERE org = :org';
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":org", $_SESSION['org'], PDO::PARAM_STR);
        $statement->execute(); 
        //SQLはexecuteして、一度fetchされると２度めのfetchにはもう一回executeが必要っぽい
        $output=fopen("History.csv","w");
        $csvheader = ["名前", "日付", "出退", "時刻"];
        $csvheader = mb_convert_encoding($csvheader, 'CP932', 'UTF-8');
        fputcsv($output,$csvheader,',');
        while($rowcsv = $statement->fetch(PDO::FETCH_ASSOC)){
            unset($rowcsv['id']);
            unset($rowcsv['org']);
            unset($rowcsv['userID']);
            $rowcsv = mb_convert_encoding($rowcsv, 'CP932', 'UTF-8');
            fputcsv($output,$rowcsv,',');           
            }
            fclose($output); 
        ?>
        <?php //チャート用書き込み処理
            date_default_timezone_set ('Asia/Tokyo');
            $thisMonth = date("Y-m");

            $outputgcsv = fopen("google.csv", "w");
            $csvheader = ["日付", "勤務時間"];
            $dbh = db_open();
            fputcsv($outputgcsv,$csvheader,',');
            $diffSUM = 0;

            $sql = 'SELECT date,diff FROM kinmujikan WHERE name = :name ORDER BY date ASC' ;
            $statement = $dbh->prepare($sql);
            $statement->bindParam(":name", $_SESSION['name'], PDO::PARAM_STR);
            $statement->execute(); 
            while($rowgcsv = $statement->fetch(PDO::FETCH_ASSOC)){
                //今月かそうじゃないかの判定
                if(strpos($rowgcsv['date'],$thisMonth) === false){
                    continue;
                }else{
                    fputcsv($outputgcsv, $rowgcsv);
                    $diffSUM += $rowgcsv['diff'];
                }
                
            }
             fclose($outputgcsv);
        ?>
        <button type=“button” onclick="location.href='History.csv'">csvダウンロード</button>
        <?php if(!$diffSUM == 0): ?>
        <h3>あなたの今月の合計勤務時間は<?php echo $diffSUM; ?>時間です。</h3>
        <?php require_once("chart.php"); ?>
        <div id="crt_ertdlyYY"></div>
        <?php endif; ?>
</body>

</html>
