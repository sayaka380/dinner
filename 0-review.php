<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>夕食の献立検索review</title>
    <link rel="stylesheet" type="text/css" href="0-style.css">
</head>
<body>
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
    
    <div class="result">
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
            
            //レビュー投稿の表示
            $sql = 'SELECT * FROM review';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            ?>
            <?php foreach ($results as $row):?>
            <img src="images/<?php echo $row['pic']; ?>" width="300" height="300">
            <br>
            <?php 
                echo "料理名：".$row['name'].'<br>';
                echo "点数：".$row['score']."点".'<br>';
                echo "＜感想＞".$row['cmt'];
                echo "<br><hr>";
            ?>
            <?php endforeach;?>
    </div>
</div>
</body>
</html>