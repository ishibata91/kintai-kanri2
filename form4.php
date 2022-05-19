
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
    <?php
    require_once "functions.php";
    login_check();
    if(isset($_SESSION['date'])){
    $dbh = db_open();
    //↓は今まで受けた変数を書き込みます
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

        if(!empty($_SESSION['timeDiff'])){
        $sql ="INSERT INTO kinmujikan (id,name,date,userID,diff)VALUES(NULL, :name, :date, :userID, :diff)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":name", $_SESSION['name'], PDO::PARAM_STR);
        $stmt->bindParam(":date", $_SESSION['date'], PDO::PARAM_STR);
        $stmt->bindParam(":userID", $_SESSION['uID'], PDO::PARAM_STR);
        $stmt->bindParam(":diff", $_SESSION['timeDiff'], PDO::PARAM_STR);
        $stmt->execute();
        unset($_SESSION['timeDiff']);
        }
        unset($_SESSION['date']);
        unset($_SESSION['subject']);
        unset($_SESSION['time']);
        


    }
    ?>
    

</head>
<body>
<?php require_once "header.php" ?>
    <h1>履歴</h1>
    <a href="input.php">入力画面へ</a>
    <?php
        $dbh = db_open();
        $sql = 'SELECT * FROM dakokudb WHERE org = :org';
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":org", $_SESSION['org'], PDO::PARAM_STR);
        $statement->execute();
        //queryは読み込むだけなのでbindparamを必要としない
        //なぜqueryではないのか。ログイン時に保存される組織名のセッションとDBのセッションを照合しないといけないから。
        ?>
        <table id="table_id" class="display" width="800">
        <thead>
        <tr align="center">
        <th>名前</th> 
        <th>日付</th>
        <th>出退</th>
        <th>時間</th>
        <th>削除</th>
        </tr>
        </thead>
        <tbody>
        <?php while($row = $statement->fetch(PDO::FETCH_ASSOC)): ?>
            <tr align="center">
            <td><?php echo str2html($row['name'])?></td>
            <td><?php echo str2html($row['date'])?></td>
            <td><?php echo str2html($row['subject'])?></td>
            <td><?php echo str2html($row['time'])?></td>
            <?php if($_SESSION['name']==$row['name']): ?>
            <td><a href="delete.php?id=<?php echo (int) $row['id']; ?>" onclick="return confirm('本当に削除しますか？');">削除</a></td>
            <?php else: ?>
            <td><a>削除不可</a></td>
            <?php endif; ?>
            </tr>
            <?php endwhile; ?>
        </tbody>
        </table>
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
        <h3>あなたの今月の合計勤務時間は<?php echo $diffSUM; ?>時間です。</h3>
        <?php require_once("chart.php"); ?>
        <div id="crt_ertdlyYY"></div>
</body>

</html>
