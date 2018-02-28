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
            if(!empty($_POST['name']) && !empty($_POST['comment']) && !empty($_POST['pass']) && empty($_POST['hidden'])){


                $count=$count + 1;
                $statement = $pdo->prepare("insert into keijiban_tb(id,name,comment,DATE,pass) values (:id,:name,:comment,CURRENT_TIMESTAMP,:pass)");
                $statement->bindParam(":id",$count);
                $statement->bindParam(":name", $_POST['name']);
                $statement->bindParam(":comment", $_POST['comment']);
                $statement->bindParam(":pass", $_POST['pass']);
                $statement->execute();
                $pdo = null;

            }else if(!empty($_POST['comment']) && !empty($_POST['pass']) && empty($_POST['name']) && empty($_POST['hidden'])) {


                $count=$stmt->rowCount();
                $count=$count + 1;
                $statement = $pdo->prepare("insert into keijiban_tb(id,name,comment,DATE,pass) values (:id,:name,:comment,CURRENT_TIMESTAMP,:pass)");
                $statement ->bindParam(":id",$count);
                $statement->bindParam(":name", $name, PDO::PARAM_STR);
                $statement->bindParam(":comment", $_POST['comment']);
                $statement->bindParam(":pass", $_POST['pass']);
                $name="名無しさん";
                $statement->execute();
                $pdo = null;
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

                $id = $_POST['delete'];
                //SQLステートメントを実行し、結果を変数に格納
                $sql = "select * from keijiban_tb where id=$id";
                $result = $pdo->query($sql);

                //foreachで結果を列にし、パスワード部分を抜き出す
                foreach ($result as $row) {

                    if ($row['pass'] == $_POST['delete_pass']) {

                        $sql = "delete from keijiban_tb where id= $id";
                        $result = $pdo->query($sql);
                    } else {

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


            //編集の処理

            if(!empty($_POST['edit']) && !empty($_POST['edit_pass'])) {

                $edit = $_POST['edit'];
                //SQLステートメントを実行し、結果を変数に格納
                $sql = "select * from keijiban_tb where id=$edit";
                $result = $pdo->query($sql);

                //foreachで結果を列にし、パスワード部分を抜き出す
                foreach ($result as $row) {

                    if ($row['pass'] == $_POST['edit_pass']) {

                        $edit = $row['id'];
                        $edit_name =$row['name'];
                        $edit_comment=$row['comment'];


                    } else {

                        echo "<script>alert('パスワードが異なるため編集できません');</script>";
                    }
                }
            }


            if(!empty($_POST['name']) && !empty($_POST['comment']) && !empty($_POST['hidden'])&& !empty($_POST['pass']) && !empty($_POST['edit'])) {


                //内容の変更の方法２　試し済み
                /*$edit = $_POST["edit"];
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $sql = "update keijiban_tb set name='$name', comment='$comment' where id ='$edit'";
                $result = $pdo->query($sql);

*/

                // UPDATE文を変数に格納

                $sql = 'update keijiban_tb set name =:name, comment=:comment, pass=:pass, where id = :value';
                //SQLを準備する

                $stmt = $pdo->prepare($sql);

                    $name = $_POST['name'];
                    $comment = $_POST['comment'];
                    $pass=$_POST['pass'];
                    $edit=$_POST['edit'];

                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                    $stmt->bindParam(':value', $edit, PDO::PARAM_STR);


                //prepare で書かれたものをここで実行する
                $stmt->execute();

                // 更新完了のメッセージ
                echo '更新完了しました';

                //名無しさん

            }elseif(empty($_POST['name']) && !empty($_POST['comment']) && !empty($_POST['hidden'])&& !empty($_POST['pass']) && !empty($_POST['edit'])) {


                // UPDATE文を変数に格納

                $sql = 'update keijiban_tb set name =:name, comment=:comment, pass=:pass, where id = :value';
                //SQLを準備する
                $stmt = $pdo -> prepare($sql);

                $name = "名無しさん";
                $comment = $_POST['comment'];
                $pass=$_POST['pass'];
                $edit=$_POST['edit'];

                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);

                //prepare で書かれたものをここで実行する
                $stmt->execute();

                // 更新完了のメッセージ
                echo '更新完了しました';

            }

            ?>
        </div>

	        <div class="comment-form">
	            <h2>メッセージを送る</h2>
	            <form action="kadai.php" method="post">
		            user name
		            <input type="text" name="name" class="name" value="<?php echo $edit_name;?>">
		            message
		            <textarea name="comment" class="comment" value="" placeholder="<?php echo $edit_comment; ?>"></textarea>
                    password
                    <input type="text" name="pass" class="pass">
                    <input type="hidden" name="hidden" value="<?php echo $edit; ?>">
		            <button type="submit" class="btn btn-comment">Submit</button>
	           	</form>
	        </div>


        <div class="delete-container">
            <div class="delete-form">
                <h4>メッセージを削除する</h4>

                <form action="kadai.php" method="post">
                    Number
                    <input type="text" name="delete" class="delete">
                    Password
                    <input type="text" name="delete_pass" class="delete_pass">

                    <button type="submit" class="btn btn-comment">削除</button>
                </form>
            </div>

        </div>

        <div class="edit-container">
            <div class="edit-form">
                <h4>メッセージを編集する</h4>

                <form action="kadai.php" method="post">
                    Number
                    <input type="text" name="edit" class="edit" value="<?php echo $edit; ?>">
                    Password
                    <input type="text" name="edit_pass" class="edit_pass">
                    <button type="submit" class="btn btn-comment">変更</button>
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

