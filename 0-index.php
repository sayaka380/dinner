<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>夕食の献立検索</title>
    <link rel="stylesheet" type="text/css" href="0-style.css">
</head>
<body>
<?php
    //フラグ初期化
    $err_flg = "n";   //エラーチェック
    $send_flg = "n";  //ログインチェック
    $reg_flg = "n";   //登録チェック
    
    //DB接続設定
    $dsn = 'mysql:dbname=***;host=localhost;charset=utf8mb4';
    $user = '***';
    $password = '***';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    $tb_name = "user";    //ユーザー管理テーブル

    //DB内にテーブルがなければ作成する
    $sql="CREATE TABLE IF NOT EXISTS user"
    ."("
    ."num INT AUTO_INCREMENT PRIMARY KEY,"
    ."id INT UNSIGNED,"
    ."pass char(10),"
    ."name char(10)"
    .");";
    $stmt = $pdo->query($sql);
    
    
?>

<div class="all">
    <!--サービス名-->
    <h1 class="service">あなたの夕食決めちゃいますっっっ！！</h1>
    <!--<hr width="90%">-->
    
    <!--サービスの説明-->
    <div class="content">
        <p>毎日の夕食を決めるのって、大変じゃないですか？<br>
           ここは、そんな方に向けて、夕食を決めてくれる検索サイトです。<br>
           今日の気分を基に、予算や時間が一致する料理が表示されます。<br>
           表示された料理名をクリックすると、作り方を見ることができます。</p>
        <p>また、料理名でも検索できます。<br>
           同じ料理でも、味付けの違いなどがあるので、是非活用してみてください！</p>
        <p>レビュー投稿機能もあるので、実際に作ってみての感想を見ることができます。<br>
           あなたも料理をした際には、投稿してみてください！</p>
    </div>
    <hr width="90%">
    
    <!--ユーザー登録＆ログイン-->
    <div class="user">
        <p>このサービスを利用するには、ユーザー登録が必要です。<br>
        登録済みの方は、IDとPWを入力しログインしてください。<br>
        初めてご利用される方は、IDとPWを新規登録してください。<br><br></p>

        <!--ログイン-->
        <div class="form">
            <form action="" method="post">
            登録済みの方はこちら（IDは数字4桁、PWは10文字まで）<br>
            ID:<input type="number" name="old_id" min="1000" max="9999"
            placeholder="ID" class="textbox"><br>
            PW:<input type="password" name="old_pw" size="15" maxlength="10" 
            autocomplete="new-password" placeholder="パスワード" class="textbox">
            <input type="submit" name="in" value="ログイン" class="button">
            <br><br>

        <!--新規登録-->
            新規登録の方はこちら（IDは数字4桁、PWは10文字まで）<br>
            新規登録ID:<input type="number" name="new_id" min="1000" 
            max="9999" placeholder="新規ID" class="textbox"><br>
            新規登録PW:<input type="password" name="new_pw" size="15" 
            maxlength="10" autocomplete="new-password" 
            placeholder="新規パスワード" class="textbox"><br>
            おなまえ:<input type="text" name="new_name" size="30" maxlengtth="10"
            placeholder="おなまえ" class="textbox">
            <input type="submit" name="reg" value="登録" class="button">
            </form>
        </div>
    </div>
 
    <?php
        //ログインチェックリスト
        $chk_list1 = !empty($_POST["in"]) && $_POST["old_id"] != "" &&
        isset($_POST["old_id"]) && $_POST["old_pw"] != "" &&
        isset($_POST["old_pw"]);
        
        //ログイン機能
        if($chk_list1){
            //$_SESSION = $_POST;
            $id = $_POST["old_id"];
            $pw = $_POST["old_pw"];
            
            //入力されたidをSELECTで取得
            $sql = 'SELECT * FROM user WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $count_id = $stmt->rowCount();            
    
            //idが一致するレコードを取得できた場合、パスワードを取得
            if($count_id == 1){
                $sql = 'SELECT * FROM user WHERE id=:id and pass=:pass';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':pass', $pw, PDO::PARAM_STR);
                $stmt->execute();
                $count_pw = $stmt->rowCount();
                
                //id、パスワードが一致するレコードを取得できたらログイン
                if($count_pw == 1){
                    //echo "ログインできました。";
                    $send_flg = "y";
                    session_start();
                    $results = $stmt->fetchAll();
                    foreach ($results as $row){
                        $_SESSION['id'] = $row['id'];
                        $_SESSION['pw'] = $row['pass'];
                        $_SESSION['name'] = $row['name'];
                    }
                    //ログインできた場合にmyページに移動
                    if(isset($_SESSION['name'])){
                        header('Location: 0-mypage.php', true, 307);
                    }
                }else{
                    echo "パスワードが一致しませんでした。";
                    $err_flg = "y";
                }
            }else{
                echo "入力されたIDは存在しません。";
                $err_flg = "y";
            }
        }elseif(!empty($_POST["in"])){
            echo "IDとPWを入力してください。";
            $err_flg = "y";
        }
        
        //新規登録チェックリスト
        $chk_list2 = !empty($_POST["reg"]) && $_POST["new_id"] != "" &&
        isset($_POST["new_id"]) && $_POST["new_pw"] != "" &&
        isset($_POST["new_pw"]) && $_POST["new_name"] != "" &&
        isset($_POST["new_name"]);
        
        //新規登録
        if($chk_list2){
            $new_id = $_POST["new_id"];
            $new_pw = $_POST["new_pw"];
            $new_name = $_POST["new_name"];
            
            //入力されたidをSELECTで取得
            $sql = 'SELECT * FROM user WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $new_id, PDO::PARAM_INT);
            $stmt->execute();
            $count_id = $stmt->rowCount();            
    
            //num(ID)が存在しなかったらユーザー新規登録
            if($count_id == 1){
                echo "そのIDは使用されています。"."<br>"."別のIDで登録してください。";
                $err_flg = "y";
            }else{
                $sql = $pdo -> prepare("INSERT INTO user (id, pass, name) 
                VALUES (:id, :pass, :name)");
                $sql -> bindParam(':id', $new_id, PDO::PARAM_INT);
                $sql -> bindParam(':pass', $new_pw, PDO::PARAM_STR);
                $sql -> bindParam(':name', $new_name, PDO::PARAM_STR);
                $sql -> execute();
                
                echo "登録が完了しました。";
                $reg_flg = "y";
            }
        }elseif(!empty($_POST["reg"])){
            echo "新規登録IDとPWとおなまえを入力してください。";
            $err_flg = "y";
        }
    
    ?>
    
    
</div>
</body>
</html>
