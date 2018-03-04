<!DOCTYPE html>
<html lang="ja">
<head>
    <meta http-equiv="content-type" charset="UTF-8">
    <title>My Web Page</title>
    <link rel="stylesheet" href="kadai.css">
</head>　

<body>
<div class="comment-section">
    <div class="section-title">
        <h3>KEIJIBAN</h3>
    </div>

    <div class="comment-container">

        <?php
        //文字化け対策
        header('Content-Type: text/html; charset=UTF-8');
        $dsn='mysql:dbname=test;host=localhost;charset=utf8mb4';
        $user='root';
        $password='root';
        //PDOでDBに接続する
        $pdo= new PDO($dsn,$user,$password);
        //行数をカウントする
        $sql='SELECT * FROM keijiban_tb';
        $stmt=$pdo->query($sql);
        $stmt->execute();
        $count=$stmt->rowCount();
        //新規入力
        if(!empty($_POST['name']) && !empty($_POST['comment']) && !empty($_POST['pass'])){

            $statement = $pdo->prepare("insert into keijiban_tb(name,comment,DATE,pass) values (:name,:comment,CURRENT_TIMESTAMP,:pass)");
            $statement->bindParam(":name", $_POST['name']);
            $statement->bindParam(":comment", $_POST['comment']);
            $statement->bindParam(":pass", $_POST['pass']);
            $statement->execute();

        }else if(!empty($_POST['comment']) && !empty($_POST['pass']) && empty($_POST['name'])) {

            $statement = $pdo->prepare("insert into keijiban_tb(name,comment,DATE,pass) values (:name,:comment,CURRENT_TIMESTAMP,:pass)");
            $statement->bindParam(":name", $name, PDO::PARAM_STR);
            $statement->bindParam(":comment", $_POST['comment']);
            $statement->bindParam(":pass", $_POST['pass']);
            $name="名無しさん";
            $statement->execute();

        }
        //例外処理
        //パスワードが入ってない。
        else if(!empty($_POST["comment"]) && empty($_POST["pass"])){
            echo "<script>alert('パスワードを入力してください');</script>";
        }
        //コメントが入ってない。
        else if( !empty($_POST['name']) && empty($_POST["comment"]) && !empty($_POST["pass"])){
            echo "<script>alert('コメントを入力してください');</script>";
        }
        //匿名、コメントが入ってない。
        else if( empty($_POST['name']) && empty($_POST["comment"]) && !empty($_POST["pass"])){
            echo "<script>alert('コメントを入力してください');</script>";
        }
        //削除の処理
        if(!empty($_POST['delete']) && !empty($_POST['delete_pass'])) {
            $id= $_POST['delete'];
            //SQLステートメントを実行し、結果を変数に格納
            $sql = "select * from keijiban_tb where id=$id";
            $result = $pdo->query($sql);
            //foreachで結果を列にし、パスワード部分を抜き出す
            foreach ($result as $row) {
                if ($row['pass'] == $_POST['delete_pass']) {
                    $sql = "delete from keijiban_tb where id= $id";
                    $result = $pdo->query($sql);
                    echo "<script>alert('投稿を削除しました');</script>";
                }

                else {
                    echo "<script>alert('パスワードが異なるため削除できません');</script>";
                }
            }
        }
        //例外処理
        //パスワードが入ってない。
        else if(empty($_POST['delete']) && !empty($_POST['delete_pass'])){
            echo "<script>alert('削除したい番号を入力してください');</script>";
        }
        else if(!empty($_POST['delete']) && empty($_POST['pass'])){
            echo "<script>alert('登録したパスワードを入力してください');</script>";
        }

        ?>
    </div>

    <div class="comment-form">
        <h2>メッセージを送る</h2>
        <form action="kadai2.php" method="post">
            user name
            <input type="text" name="name" class="name">
            message
            <textarea name="comment" class="comment"></textarea>
            password
            <input type="text" name="pass" class="pass">
            <button type="submit" class="btn btn-comment">Submit</button>
        </form>
    </div>


    <div class="delete-container">
        <div class="delete-form">
            <h4>メッセージを削除する</h4>

            <form action="kadai2.php" method="post">
                Number
                <input type="text" name="delete" class="delete">
                Password
                <input type="text" name="delete_pass" class="delete_pass">

                <button type="submit" class="btn btn-comment">削除</button>
            </form>
        </div>

    </div>


</div>

<div class = "result">


    <br>
    <p>------------------------- 投稿一覧(<?php echo $count; ?>)件 -------------------------- </p>

    <?php
    //select文
    $sql='SELECT * FROM keijiban_tb order by DATE DESC';
    $results = $pdo -> query($sql);
    //実行・結果取得 //以下でブラウザ上に出力する
    foreach ($results as $row) {
        echo "<p> -------------------------------------------------------------------------</p>";
        echo $row['id']." ".$row['name'] . "  " . $row['comment'] . "<br>" . $row['date'] . "<br>";
    }
    ?>

</div>

</body>

</html>
