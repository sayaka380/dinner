<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>夕食の献立検索search</title>
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
    <div class="search">
        詳しく作り方をみたい料理番号を入力してください。
        <form action="" method="post">
            作り方を見る料理番号：
            <input type="number" name="see_num" min="1" max="9999" placeholder="1" class="textbox">
            <input type="submit" name="see" value="作り方" class="button">
        </form>
    <?php
        //検索内容
        $mood = $_SESSION['mood'];
        $money = $_SESSION['money'];
        $time = $_SESSION['time'];
        //作り方ページへとぶ
        $chk_list1 = !empty($_POST["see"]) && $_POST["see_num"] != "" &&
        isset($_POST["see_num"]);

        if($chk_list1){
            $see_num = $_POST["see_num"];
            //入力されたnumをSELECTで取得
            $sql = 'SELECT * FROM cook WHERE cooknum=:cooknum and mood=:mood and 
            money=:money and time=:time';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':cooknum', $see_num, PDO::PARAM_INT);
            $stmt->bindParam(':mood', $mood, PDO::PARAM_STR);
            $stmt->bindParam(':money', $money, PDO::PARAM_STR);
            $stmt->bindParam(':time', $time, PDO::PARAM_STR);
            $stmt->execute();
            $count_num = $stmt->rowCount();            
    
            //numが一致するレコードを取得できた場合、作り方ページへとぶ
            if($count_num == 0){
                echo "入力された料理番号の料理は存在しません。";
                $err_flg ="y";
            }else{
                header('Location: 0-search2.php', true, 307);
                exit();
            }
        }elseif(!empty($_POST["see"])){
            echo "作り方が見たい料理番号を入力してください。";
            $err_flg ="y";
        }
    ?>
    <hr size="10" color="#0000ff" noshade>
    <?php 
        //検索内容と一致するレコードを抽出
        $sql = 'SELECT * FROM cook WHERE mood=:mood and money=:money 
        and time=:time';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':mood', $mood, PDO::PARAM_STR);
        $stmt->bindParam(':money', $money, PDO::PARAM_STR);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
        $stmt->execute();
        $count_cook = $stmt->rowCount();
            
        //peopleと一致するレコードを表示
        if($count_cook == 0){
            echo "ただいま準備中です。"."<br>"."他の条件で検索してください。";
            $err_flg = "y";
        }else{
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                echo "料理番号：".$row['cooknum'].'<br>';
                echo "料理名：".$row['name'];
                echo "＜".$row['mood']."＞".'<br>';
                echo $row['money']."（1人前の費用）".',';
                echo $row['time']."で作れる！".'<br>';
                /*echo "＜材料＞".'<br>'.nl2br($row['material']).'<br>';
                echo "＜作り方＞".'<br>'.nl2br($row['process']).'<br>';*/
                echo "<hr>";
            }
        }
    ?>
    </div>
</div>
    
</body>
</html>