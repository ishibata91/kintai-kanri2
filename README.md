# kintai-kanri2
コンセプトはちゃんと使おうと思えば使える勤怠アプリです。
<br>
今回考えた必要な機能は
<br>
1誰のデータかわかる機能
<br>
2履歴としてデータを参照できる
<br>
3データをアプリ外部で管理できるためにエクセルなどにエクスポートできる機能
<br>
4管理者が機能を変えたいと思った時に自由に変えられる、ある種個性を持たせられる機能（未達成）
です。
<br>
1,2は出勤、退勤の日付、時間を名前ごとに登録して履歴として参照することができるようにしました。
<br>
加えて、ソート機能をjqueryのCDNを利用して実装しました。
<br>
勤怠管理アプリとして必要最低限のレベルには達せたと思います。
<br>
3はshift-jisで出力されるようにして、かつ表にする時はUTF-8に変換されるようにしました。エクセルにそのまま読み込んで使えるはずです。
<br>
無料のphpサーバー(herokuではない)ではローカルで上手くいってたものが動かないというトラブルがありました。そのレンタルサーバーにはoutput_bufferingが無かったのでsession関係のものを全て一番上に持ってくる必要があり、そこで動かすのは不可能だと判断しました。
<br>
所詮殆どプログラミングに触れてこなかった初心者が作った稚拙なものですが、ほぼ全ての使える時間を使って勉強したので見ていただけると幸いです。
<br>
-------参考------
<br>
progate:html,css,javascript,php
<br>
PHP入門 確認画面付きのお問い合わせフォームをつくりながらPHPを学ぶ（第2版） (DESIGNMAP BOOKS) DESIGNMAP
<br>
これ1冊でゼロから学べる Webプログラミング超入門　ーHTML,CSS,JavaScript,PHPをまるごとマスター 掌田 津耶乃
<br>
初心者からちゃんとしたプロになる　PHP基礎入門 柏岡 秀男
<br>
---------ここからkintai-kanri2------
<br>
前はデータ管理にCSVを直接使ってましたが、今回はmySQLを使ってデータ管理するようにしました。
<br>
加えて、ログイン、ログアウト機能をつけ、本人以外はデータの削除が出来ないようにしました。
<br>
最初はherokuでデプロイしようとしました。しかしcleardb等のアドオンを入れるまではうまくいっても、その後テーブルの編集などmysqlのcliやワークショップを使わないと出来ないという段階でうまくいかず断念しました。
<br>
-----5/12追記-----
<br>
同じ名前で違うパスワードみたいな登録の仕方が出来てしまっていたので同じ名前が存在する場合は登録出来ないようにしました。
<br>
処理の順番を改善しました。
<br>
php.iniでディレクトリを制限して多分requireやincludeを使った攻撃の対策をしてみました。htmlspecialcharsも一応入れてみました。
<br>
CSRFトークンもつけてみようと思ったのですが、トークン生成から、変数同じなのにPOSTに送信されるトークンが変わってしまいよくわからなかったので断念しました。
<br>
-----5/13追記------
<br>
組織名の登録を追加しました。自分の組織以外の勤怠記録を見ることが出来ないようになりました。
<br>
-----5/14追記-----
<br>
登録画面で組織名を空にして名前とパスワードを入力すれば登録されてしまっていた問題を修正しました。（空の時に処理を止めるコードが無かった）
<br>
アクセスした時点で入力されてないなどのエラーメッセージを出さないようにしました。
<br>
コードをフォーマットしてちょっときれいにしました
<br>
------5/17追記-----
<br>
セキュリティ上の問題があったのでリポジトリを削除して作り直しました
<br>
-------5/18追記-------
<br>
データ削除にGETメソッドを使用していたため、他人のデータでもリンクを書き換えれば削除できてしまう問題を修正しました。
<br>
知識不足で冗長な方法になってしまいましたが、具体的には
<br>
ユーザー登録時点で何かしらランダムで対衝突性のある文字列を出力しusersテーブルに登録する。次にログイン時usersテーブルからusernameが同じテーブルから例の文字列をPOSTし、dakokuテーブルにも登録する。それで、そのdakokuテーブルの文字列と、そのユーザーのセッションの文字列が同一でないと削除できなくする。
<br>
としました。したがって、今まであったDBのデータを全て削除しました。
<br>
--------5/19追記-------
<br>
同日で、出退勤が同じなら打刻できないようにしました。
<br>
入力画面で、今日の日付・現在の時刻・現在の時刻が12時前なら出勤・12時以降なら退勤が初期状態で選ばれるようにしました。
<br>
予定:より使われるようにするにはと考え、現在
<br>
データからより多くの視覚的情報を得られるようにし、かつそれをすぐに共有できる機能をつける
<br>
など予定してます
<br>
----5/20追記-----
<br>
出勤からしか登録出来ないようにしました。
<br>
勤務時間を出退勤から引き算で求めるようにしました。
<br>
出勤、退勤どちらのデータでも削除すると対応する勤務時間は削除されます。
<br>
チャートは実装しましたが、jsを使い慣れてないこともあり設定の変え方すら理解出来なかったのでcsvファイルの書式合わせと参照ファイル書き換え以外はコピペです。
<br>
チャートのAPIはgoogle charts toolを使用しました。
<br>
予定:カレンダーで履歴表示をできるようにしてみたいと思います
<br>
-----5/23追記-----
<br>
カレンダーは実装に20時間以上かかりました。知識不足であまりに冗長になり、可読性が絶望的に悪くなってしまったので今度整理しようと思います。
<br>
今までの機能（削除機能は編集機能に差し替えました）は全て維持したまま実装しました。
<br>
多分バグだらけだと思うので変なことすると壊れると思います。
<br>
https://ishiba91.conohawing.com/
