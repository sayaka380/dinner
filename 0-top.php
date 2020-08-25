<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>夕食の献立検索topページ</title>
    <link rel="stylesheet" type="text/css" href="0-style.css">
</head>
<body>
<?php
    //フラグ初期化
    $err_flg = "n";
    $send_flg = "n";
    $search_flg = "n";
    $sub_flg = "n";
?>
<div class="all">
    <!--サービス名-->
    <h1 class="service">あなたの夕食決めちゃいますっっっ！！</h1>
    <!-- メインメニュー -->
    <ul id="menu">
    <li><a href="0-mypage.php">マイページ</a></li>
    <li><a href="0-top.php">検索ページ</a></li>
    <li><a href="0-review.php">レビュー一覧</a></li>
    <li><a href="0-index.php">ログアウト</a></li>
    </ul>
    <?php
        session_start();
        /*ユーザー情報保持
        $_SESSION['id']
        $_SESSION['pw']
        $_SESSION['name']*/
        
        //ログインしているかのチェック
        if(isset($_SESSION['name'])){
            //DB接続設定
            $dsn = 'mysql:dbname=***;host=localhost;charset=utf8mb4';
            $user = '***';
            $password = '***';
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            $tb_name = "user";    //ユーザー管理テーブル
        }else{
          header('Location:0-index.php');
          exit;
        }
    ?>
    <!--あいさつ-->
    <div class="greeting">
        <p><?php echo $_SESSION['name'] ?>さんログインありがとうございます。<br>
        このページでは、夕食検索機能や、キーワード検索、レビュー投稿が行えます。<br>
        是非ご活用ください！！</p>
    </div>
    <hr width="90%">
    
    <!--献立検索-->
    <div class="dinner">
        <p>今日の気分と1人当たりの予算、料理時間を入力してください。<br></p>
        <form action="" method="post">
        
        <!--入力フォーム-->
        <div class="form">
            今日の気分
            <select name="mood" class="textbox">
                <option>和食</option>
                <option>洋食</option>
                <option>中華</option>
                <option>イタリアン</option>
            </select><br>
            1人当たりの予算
            <select name="money" class="textbox">
                <option>300円以内</option>
                <option>300円以上</option>
            </select><br>
            料理時間
            <select name="time" class="textbox">
                <option>30分以内</option>
                <option>30分以上</option>
            </select>
            <input type="submit" name="send" class="button">
        </div>
        </form>
    </div>
    <?php
        if(!empty($_POST["send"])){
            $send_flg = "y";
            $_SESSION['mood'] = $_POST["mood"];
            $_SESSION['money'] = $_POST["money"];
            $_SESSION['time'] = $_POST["time"];
            header('Location: 0-search.php', true, 307);
            exit();
        }
    ?>
    <hr width="90%">
    
    <!--キーワード検索-->
    <div class="keyword">
        <p>料理名でも検索できます。下の料理名を選び検索してください。</p>
        <p class="info_p">炊き込みご飯/豚汁/肉じゃが/ハンバーグ/オムライス/カレー/ビーフシチュー/
        チャーハン/餃子/麻婆豆腐/グラタン</p>
        <form action="" method="post">
        
        <!--入力フォーム-->
        <div class="form">
            <input type="search" name="search" placeholder="キーワードを入力"
            class="textbox">
            <input type="submit" name="searching" value="検索" class="button">
        </div>
        </form>
        
    <?php
        $chk_list1 = !empty($_POST["searching"]) && $_POST["search"] != "" &&
        isset($_POST["search"]);
    
        if($chk_list1){
            $search_flg = "y";
            header('Location: 0-keyword.php', true, 307);
            exit();
        }elseif(!empty($_POST["searching"])){
            echo "料理名を入力してください。";
            $err_flg ="y";
        }
    ?>
    </div>
    <hr width="90%">
</div>
</body>
</html>