<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>夕食の献立検索keyword</title>
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
            
            //検索内容
            if(!empty($_POST["search"])){
                $search = '%'.$_POST["search"].'%';

                //検索内容と一致するレコードを抽出
                $sql = 'SELECT * FROM cook  WHERE name LIKE :name';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $search, PDO::PARAM_STR);
                $stmt->execute();
                $count_cook = $stmt->rowCount();
                    
                //検索内容と一致するレコードを表示
                if($count_cook == 0){
                    echo "その料理名の説明は存在しません。";
                    $err_flg = "y";
                }else{
                    $results = $stmt->fetchAll();
                    foreach ($results as $row){
                        echo "料理名：".$row['name'];
                        echo "＜".$row['mood']."＞".'<br>';
                        echo $row['money']."（1人前の費用）".',';
                        echo $row['time']."で作れる！".'<br>';
                        echo "<hr>";
                        echo "＜材料＞".'<br>'.nl2br($row['material']).'<br>';
                        echo "<hr>";
                        echo "＜作り方＞".'<br>'.nl2br($row['process']).'<br>';
                    }
                }
            }
        ?>
    </div>    
    <!--レビュー投稿フォーム-->
    <div class="review">
        <p>実際に作り方を見て作った料理を投稿することができます。</p>
        <form action="" method="post" enctype="multipart/form-data">
        
        <!--入力フォーム-->
        <div class="form">
            料理名:<input type="text" name="cookname" size="30" maxlengtth="30"
            placeholder="例）ハンバーグ" class="textbox"><br>
            点数(10点満点):<input type="number" name="score" min="0" max="10"
            placeholder="10" class="textbox"><br>
            写真:
            <input type="file" name="image" required /><br>
            感想:<br>
            <textarea name="cmt" rows="5" cols="40"
            placeholder="ここに感想を記入してください。"></textarea>
            <input type="submit" name="submit" value="投稿" class="button">
        </div>
        </form>
        
        <?php
            //DB内にテーブルがなければ作成する
            $sql="CREATE TABLE IF NOT EXISTS review"
            ."("
            ."num INT AUTO_INCREMENT PRIMARY KEY,"
            ."id INT UNSIGNED,"
            ."name char(30),"
            ."score INT UNSIGNED,"
            ."cmt TEXT,"
            ."pic TEXT"
            .");";
            $stmt = $pdo->query($sql);
            
            //画像のアップロード
            if (isset($_POST["submit"])){
                $image = uniqid(mt_rand(), true);//ファイル名をユニーク化
                //アップロードされたファイルの拡張子を取得
                $image .= '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
                $file = "images/$image";
                //ファイルが選択されていれば$imageにファイル名を代入
                if (!empty($_FILES['image']['name'])) {
                    //imagesディレクトリにファイル保存
                    move_uploaded_file($_FILES['image']['tmp_name'], './images/' . $image);
                    //画像ファイルかのチェック
                    if (exif_imagetype($file)) {
                        $message = '画像をアップロードしました';
                        $image_err_f = "true";
                        $stmt->execute();
                    } else {
                        $message = '画像ファイルではありません';
                    }
                }
                echo $message."<br>";
            }
            
            $chk_list = !empty($_POST["submit"]) && $_POST["cookname"] != "" &&
            isset($_POST["cookname"]) && $_POST["score"] != "" && isset($_POST["score"]) &&
            isset($_POST["cmt"]) && $_POST["cmt"] != "";
        
            if($chk_list){
                $cookname = $_POST["cookname"];
                $score = $_POST["score"];
                $cmt = $_POST["cmt"];
                $id = $_SESSION['id'];
                //書き込み
                $sql = $pdo -> prepare("INSERT INTO review (id, name, score, cmt,
                pic)  VALUES (:id, :name, :score, :cmt, :pic)");
                $sql -> bindParam(':id', $id, PDO::PARAM_INT);
                $sql -> bindParam(':name', $cookname, PDO::PARAM_STR);
                $sql -> bindParam(':score', $score, PDO::PARAM_INT);
                $sql -> bindParam(':cmt', $cmt, PDO::PARAM_STR);
                $sql -> bindParam(':pic', $image, PDO::PARAM_STR);
                $sql -> execute();
                
                echo "投稿が完了しました。"."<br>";
            }elseif(!empty($_POST["submit"])){
                echo "記入事項を全て入力してください。";
            }
        ?>
    </div>    
    <hr width="90%">
</div>
</body>
</html>