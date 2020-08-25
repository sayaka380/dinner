<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>夕食の献立検索</title>
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
    
    <h2>料理の情報を書き込む</h2>
    <form action="" method="post">
        <!--入力フォーム-->
        <div class="form">
            料理名:<input type="text" name="name" size="30" maxlengtth="30"
            placeholder="例）ハンバーグ" class="textbox"><br>
            気分:<input type="text" name="mood" size="30" maxlengtth="3" 
            placeholder="和" class="textbox"><br>
            お金:<input type="text" name="money" size="30" maxlengtth="10"
            placeholder="100円以下" class="textbox"><br>
            時間:<input type="text" name="time" size="30" maxlengtth="10"
            placeholder="10分以内" class="textbox"><br>
            材料:<br>
            <textarea name="material" rows="20" cols="40"
            placeholder="たまねぎ2個。"></textarea><br>
            作り方:<br>
            <textarea name="process" rows="20" cols="40"
            placeholder="たまねぎをみじん切りにする。"></textarea>
            <input type="submit" name="submit" value="投稿" class="button">
        </div>
    </form>
    
    <?php
        //DB接続設定
        $dsn = 'mysql:dbname=***;host=localhost;charset=utf8mb4';
        $user = '***';
        $password = '***';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $tb_name = "cook";    //ユーザー管理テーブル

        $name = $_POST["name"];
        $mood = $_POST["mood"];
        $money = $_POST["money"];
        $time = $_POST["time"];
        $material = $_POST["material"];
        $process = $_POST["process"];

        //DB内にテーブルがなければ作成する
        $sql="CREATE TABLE IF NOT EXISTS cook"
        ."("
        ."cooknum INT AUTO_INCREMENT PRIMARY KEY,"
        ."name char(30),"
        ."mood char(5),"
        ."money char(10),"
        ."time char(10),"
        ."material TEXT,"
        ."process TEXT"
        .");";
        $stmt = $pdo->query($sql);
        
        $chk_list = !empty($_POST["submit"]) && isset($_POST["name"]) &&
        $_POST["name"] != "" && isset($_POST["mood"]) && $_POST["mood"] != ""
        && isset($_POST["money"]) && $_POST["money"] != "" && isset($_POST["time"]) &&
        $_POST["time"] != "" && isset($_POST["material"]) && $_POST["material"] != ""
        && isset($_POST["process"]) && $_POST["process"] != "";
        
        if($chk_list){
            //書き込み
            $sql = $pdo -> prepare("INSERT INTO cook (name, mood, money, time, material,
            process)  VALUES (:name, :mood, :money, :time, :material, :process)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':mood', $mood, PDO::PARAM_STR);
            $sql -> bindParam(':money', $money, PDO::PARAM_STR);
            $sql -> bindParam(':time', $time, PDO::PARAM_STR);
            $sql -> bindParam(':material', $material, PDO::PARAM_STR);
            $sql -> bindParam(':process', $process, PDO::PARAM_STR);
            $sql -> execute();
            
            echo "登録が完了しました。"."<br>";
        }
        //登録確認
        $sql = 'SELECT * FROM cook';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo "料理番号：".$row['cooknum'].'<br>';
            echo "料理名：".$row['name'].'<br>';
            echo "気分：".$row['mood'].'<br>';
            echo "お金：".$row['money'].'<br>';
            echo "時間：".$row['time'].'<br>';
            echo "＜材料＞".'<br>'.nl2br($row['material']).'<br>';
            echo "＜作り方＞".'<br>'.nl2br($row['process']).'<br>';
            echo "<hr>";
        }
    ?>
</div>
    
</body>
</html>