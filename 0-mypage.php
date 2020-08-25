<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>夕食の献立検索mypage</title>
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
    <li><a href="0-search.php">レビュー一覧</a></li>
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
        
        //フラグ初期化
        $err_flg = "n";
        $edi_flg = "n";
        
    ?>
    <!--ユーザー情報変更-->
    <div class="info">
        <h2><?php echo $_SESSION['name'] ?>さんようこそ！</h2>
        <P class="info_p">
            ログインありがとうございます。<br>
            ユーザー情報の変更が行えます。変更事項を記入し、送信してください。
        </P>
        <div class="form">
            <form action="" method="post">
            <!--なまえの変更-->
            なまえの変更:<input type="text" name="e_name" size="30" maxlengtth="10"
            placeholder="おなまえ" class="textbox">
            <input type="submit" name="edit_na" value="変更" class="button"><br><br>
            
            <?php
                //なまえの変更
                $chk_list1 = !empty($_POST["edit_na"]) && $_POST["e_name"] != "" &&
                isset($_POST["e_name"]);

                if($chk_list1){
                    $e_name = $_POST["e_name"];
                    $id = $_SESSION['id'];
                    $sql = 'UPDATE user SET name=:name WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->bindParam(':name', $e_name, PDO::PARAM_STR);
                    $stmt->execute();
                    
                    echo $e_name."さんに変更しました。再度ログインすると
                    表示が変更されます。"."<br>";
                }elseif(!empty($_POST["edit_na"])){
                    echo "変更するおなまえを入力してください。"."<br>";
                    $err_flg ="y";
                }
            ?>
            
            <!--パスワードの変更-->
            PWの変更:<input type="password" name="now_pw" size="15" maxlength="10" 
            autocomplete="new-password" placeholder="現在のパスワード"
            class="textbox"><br>
            &emsp;&emsp;&emsp;&emsp;&emsp;<input type="password" name="e_pw"
            size="15" maxlength="10" autocomplete="new-password"
            placeholder="変更するパスワード" class="textbox">
            <input type="submit" name="edit_pw" value="変更" class="button">
            </form>
        </div>
            <?php
                //パスワードの変更
                $chk_list2 = !empty($_POST["edit_pw"]) && $_POST["now_pw"] != "" &&
                isset($_POST["now_pw"]) && $_POST["e_pw"] != "" && isset($_POST["e_pw"]);

                if($chk_list2){
                    $now_pw = $_POST["now_pw"];
                    $e_pw = $_POST["e_pw"];
                    $id = $_SESSION['id'];
                    
                    if($now_pw == $_SESSION['pw']){
                        $sql = 'UPDATE user SET pass=:pass WHERE id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->bindParam(':pass', $e_pw, PDO::PARAM_STR);
                        $stmt->execute();
                        
                        echo "パスワードの変更が完了しました。"."<br>";
                    }else{
                        echo "現在のパスワードが違います。".
                        "もう一度入力してください。"."<br>";
                    }
                }elseif(!empty($_POST["edit_pw"])){
                    echo "現在のパスワードと変更するパスワードを入力して
                    ください。"."<br>";
                    $err_flg ="y";
                }
            ?>
    </div>
    <hr width="90%">
    <div class="myroom">
        <h2><?php echo $_SESSION['name'] ?>さんの投稿！</h2>
        <?php
            $id = $_SESSION['id'];
            //レビュー投稿の表示
            $sql = 'SELECT * FROM review WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll();
            $count_re = $stmt->rowCount();
                
            //id、パスワードが一致するレコードを取得できたらログイン
            if($count_re == 0){
                echo "まだ投稿がありません。";
            }else{
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
                <?php endforeach;
            }
        ?>
    </div>
</div>
</body>
</html>