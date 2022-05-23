
<?php 
//時間関係の変数定義中心の処理
date_default_timezone_set('Asia/Tokyo');
if (isset($_GET['ym'])) {
    $ym = $_GET['ym'];
    } else {
    $ym = date('Y-m');
    }
    $timestamp = strtotime($ym);

    $today = date('Y-m-j');
    $html_title = date('Y年n月', $timestamp);
    $prev = date('Y-m', strtotime('-1 month', $timestamp));
    $next = date('Y-m', strtotime('+1 month', $timestamp));
    $day_count = date('t', $timestamp);

    $youbi = date('w', $timestamp);
    $weeks = [];
    $week = '';
//----------------------------
        //カレンダーに記録が存在する日に名前を表示するためのデータ取得
        $dbh = db_open();
        $sql = 'SELECT name,date FROM dakokudb WHERE org = :org ORDER BY date ASC';
        $statement = $dbh->prepare($sql);
        $statement->bindParam(":org", $_SESSION['org'], PDO::PARAM_STR);
        $statement->execute();
        while($line = $statement->fetch(PDO::FETCH_ASSOC)){
            $line_array[] = $line;
        }
        //重複を排除
        $line_array = array_unique($line_array,SORT_REGULAR );
   //---------------------------------------------------------     
 
//ここからはカレンダーの内容を実際に書く
//ファーストフラグ       
$isFirst = true;      
//$youbi 日曜=0 水曜 = 4 で、前の曜日分からのセル追加
$week .= str_repeat('<td></td>', $youbi);
//　for(初期化,範囲,条件式)　(１日目から,day_countで取得した月の日数以下にdayがなるまで,日をインクリメント、曜日をインクリメント。)
for ( $day = 1; $day <= $day_count; $day++, $youbi++ ){
    // $dateに日を追加。今までは変数定義時点で年月だけじゃないと月の切り替え機能ができなかった。sprintf→例(1を01に)
    $date = $ym . '-' . sprintf('%02d', $day);
    //-----------------------------------------------------------------------
    //今日の日付とfor文で出力する日付がおなじになった場合。つまりタイムゾーン日本で今日はオレンジ色になる
    if ($today == $date) {
        // 今日の日付の場合は、class="today"をつける
        $week .= '<td class="today">' . $day;
        //重複を排除した、データの配列分だけ上から出力しながら繰り返す。
        foreach($line_array as $line){
            //もしカレンダーを出力しているときにその日付と、上で取得したデータの日付が同じだった場合
            if($line['date'] == $date){
                //detailsを最初だけ出力する。これがないとそこに打刻している人が二人以上いたら人数分のプルダウンが出てくる。
                if($isFirst){
                    $week .= '<details><summary>記録</summary>';
                    $isFirst = false;
                }
                //ツールチップ用。時間と出退勤時間のデータを取得。ORDER　BYがないと出勤退勤が逆になったりする
                    $sql = 'SELECT subject,time,id FROM dakokudb WHERE name = :name AND date = :date ORDER BY date ASC, subject ASC';
                    $statement = $dbh->prepare($sql);
                    $statement->bindParam(":date", $line['date'], PDO::PARAM_STR);
                    $statement->bindParam(":name", $line['name'], PDO::PARAM_STR);
                    $statement->execute();
                    while($result = $statement->fetch(PDO::FETCH_ASSOC)){
                        $result_array[] = $result;
                    }
                    //配列に代入した時間と出退勤のデータの”一つ目だけ”出力する。出勤しかない場合といった感じ
                    $week .= '<div class="tooltip3">'.$line['name'].'<span class="description3">'.
                    $result_array[0]['subject'].'-'.$result_array[0]['time'];
                    //自分のだけは編集ボタンが出てくる                  
                        if($_SESSION['name']==$line['name']){
                            $week .= '<a href=edit.php?id='.$result_array[0]['id'].'>編集</a>';
                        }
                        //出勤だけじゃなくて退勤のデータが出てきたらこれが出力される
                        if(isset($result_array[1])){
                            $week .= '<br>'.$result_array[1]['subject'].'-'.$result_array[1]['time'];
                                if($_SESSION['name']==$line['name']){
                                $week .= '<a href=edit.php?id='.$result_array[1]['id'].'>編集</a>';
                                }
                        }
                    $week .= '</span></div>';
                    //unsetしとかないとforで回るたびに配列の中身が増え続けて正しく処理できなくなる。
                    unset($result_array);
                }
        }
        //for文の外。ファーストフラグをここでtrueにしとかないとプルダウンが最初の一個だけとかになる
        $isFirst = true;
    }else{
        //上のstr_repeatと中身は同じだが上は最初の日付と曜日の並びの整合性を取るための文。
        $week .= '<td>' . $day;
        //重複を排除した、データの配列分だけ上から出力しながら繰り返す。
        foreach($line_array as $line){
            //もしカレンダーを出力しているときにその日付と、上で取得したデータの日付が同じだった場合
            if($line['date'] == $date){
                //detailsを最初だけ出力する。これがないとそこに打刻している人が二人以上いたら人数分のプルダウンが出てくる。
                if($isFirst){
                    $week .= '<details><summary>記録</summary>';
                    $isFirst = false;
                }
                //ツールチップ用。時間と出退勤時間のデータを取得。ORDER　BYがないと出勤退勤が逆になったりする
                    $sql = 'SELECT subject,time,id FROM dakokudb WHERE name = :name AND date = :date ORDER BY date ASC, subject ASC';
                    $statement = $dbh->prepare($sql);
                    $statement->bindParam(":date", $line['date'], PDO::PARAM_STR);
                    $statement->bindParam(":name", $line['name'], PDO::PARAM_STR);
                    $statement->execute();
                    while($result = $statement->fetch(PDO::FETCH_ASSOC)){
                        $result_array[] = $result;
                    }
                    //配列に代入した時間と出退勤のデータの”一つ目だけ”出力する。出勤しかない場合といった感じ
                    $week .= '<div class="tooltip3">'.$line['name'].'<span class="description3">'.
                    $result_array[0]['subject'].'-'.$result_array[0]['time'];
                    //自分のだけは編集ボタンが出てくる                  
                        if($_SESSION['name']==$line['name']){
                            $week .= '<a href=edit.php?id='.$result_array[0]['id'].'>編集</a>';
                        }
                        //出勤だけじゃなくて退勤のデータが出てきたらこれが出力される
                        if(isset($result_array[1])){
                            $week .= '<br>'.$result_array[1]['subject'].'-'.$result_array[1]['time'];
                                if($_SESSION['name']==$line['name']){
                                $week .= '<a href=edit.php?id='.$result_array[1]['id'].'>編集</a>';
                                }
                        }
                    $week .= '</span></div>';
                    //unsetしとかないとforで回るたびに配列の中身が増え続けて正しく処理できなくなる。
                    unset($result_array);
                }
        }
        //for文の外。ファーストフラグをここでtrueにしとかないとプルダウンが最初の一個だけとかになる
        $isFirst = true;   
    }
    $week .= '</details>';
    $week .= '</td>';
    //---------------------------------------------------------------------
    //ここからは最終日あとに空の枠を追加するための処理
     // 週終わり、または、月終わりの場合\
     //youbi÷7あまり６にしてるのは、曜日がインクリメントされるので6以上も出てくるからその対策である。
        if ($youbi % 7 == 6 || $day == $day_count) {
            if ($day == $day_count) {
                // 月の最終日の場合、空セルを追加
                // 例）最終日が水曜日の場合、木・金・土曜日の空セルを追加
                $week .= str_repeat('<td></td>', 6 - $youbi % 7);
            }
            // もし最終日でなく、土曜であればそこでweekをテーブルのrawとする。
            $weeks[] = '<tr>' . $week . '</tr>';

            // weekをリセット
            $week = '';
        }
    //これ最初のfor
    }
?>
<!-- なぜかはわからないが外部css読み込みだと適用されないので直接書いた。 -->
<style>
    .container {
    font-family: 'Noto Sans JP', sans-serif;
}
a {
    text-decoration: none;
}
th {
    height: 30px;
    text-align: center;
}
.today {
    background: orange !important;
}
th:nth-of-type(1), td:nth-of-type(1) {
    color: red;
}
th:nth-of-type(7), td:nth-of-type(7) {
    color: blue;
}
.name{
    font-size: 4px;
    padding: 1px;
    margin: 1px;
    background-color: rgba(130, 200, 158, 0.5);
    line-height:90%;
}
.subject{
    font-size:3px;
    padding: 1px;
    margin: 1px;
    background-color: rgba(130, 158, 158, 0.5);
    line-height:90%;
}
table{
    table-layout: fixed;
}
td {
    height: 100px;
    width: 100px;
    overflow-wrap :normal;  
}
.tooltip3{
    position: relative;
    cursor: pointer;
    display: inline-block;
    font-size: 7px;
    padding: 10px;
    margin: 10px;
    background-color: rgba(130, 200, 158, 0.5);
    line-height:90%;
    
}
.tooltip3 p{
    margin:0;
    padding:0;
}
.description3 {
    display: none;
    position: absolute;
    padding: 10px;
    font-size: 12px;
    line-height: 1.6em;
    color: #fff;
    border-radius: 5px;
    background: #000;
    width: 150px;
}
.description3:before {
    content: "";
    position: absolute;
    top: -17px;
    right: 60%;
    border: 15px solid transparent;
    border-top: 15px solid #000;
    margin-left: -15px;
    transform: rotateZ(180deg);
}
.tooltip3:hover .description3{
    display: inline-block;
    top: 15px;
    left: 0px;
}

</style>