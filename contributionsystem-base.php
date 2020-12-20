<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
    <meta name ="viewport" content="width=device-width,initial-scale=1.0">
    <title>mission_5-1</title>
</head>
<body>
<?php
// DB接続設定
$dsn = 'mysql:dbname="データベース名";host=localhost';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS mission5_1"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
    . "comment TEXT,"
    . "pass char(16),"
    . "date DATETIME"
    .");";
    //テーブルの表示　※確認用、あとで消す
    $stmt = $pdo->query($sql);
    $sql ='SHOW TABLES';
	$result = $pdo -> query($sql);
	foreach ($result as $row){
		echo $row[0];
		echo '<br>';
	}
    echo "<hr>";
    //テーブルの構成詳細表示　※確認用、あとで消す
    $sql ='SHOW CREATE TABLE mission5_1';
	$result = $pdo -> query($sql);
	foreach ($result as $row){
		echo $row[1];
	}
    echo "<hr>";
//編集のための準備
if(!empty($_POST["enum"])){//編集フォームが空じゃないとき
    $id=$_POST["enum"];
    $sql = 'SELECT * FROM mission5_1 WHERE id=:id ';//データの取得
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する
        $results = $stmt->fetchAll(); 
        foreach ($results as $row){
         if($row['pass']==$_POST["epass"]){//パスワードが一致したとき
            //データの送信
            $editnum=$row['id'];
            $editname=$row['name'];
            $editcomment=$row['comment'];
         }else{
            if(empty($_POST["epass"])) echo "パスワードを入力してください<br>";//パスワード未入力
            else echo "パスワードが違います<br>";//パスワードが間違っている
           //$setenum=$row['id'];//編集番号指定フォームに送信　※あとで修正
         }
        }
    }
//投稿
if(!empty($_POST["name"])||!empty($_POST["comment"])&&!empty($_POST["pass"]))
{//投稿フォームが空じゃないとき
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $pass=$_POST["pass"];
    $date=date('Y/m/d H:i:s');
if(!empty($_POST["getnum"])){//投稿フォームの編集指定番号が空じゃないとき
    $id=$_POST["getnum"];
    $sql = 'SELECT * FROM mission5_1 WHERE id=:id ';//データの取得
    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
    $stmt->execute();                             // ←SQLを実行する
    $results = $stmt->fetchAll(); 
    foreach ($results as $row)
     if($row['pass']==$pass){//パスワードが一致したとき
    //編集
	$sql = 'UPDATE mission5_1 SET name=:name,comment=:comment WHERE id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
     }else{
        if(empty($pass)) echo "パスワードを入力してください<br>";//パスワード未入力
        else echo "パスワードが違います<br>";//パスワードが間違っている
       //$setenum=$row['id'];//編集番号指定フォームに送信　※あとで修正
     }
    }else{
    //データレコードの登録
    $sql = $pdo -> prepare("INSERT INTO mission5_1 (name, comment,pass,date) 
    VALUES (:name, :comment,:pass,:date)");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
    $sql-> bindParam(':pass',$pass,PDO::PARAM_STR);
    $sql->bindParam(':date',$date,PDO::PARAM_STR);
    $sql -> execute();
    }
}
//削除
    if(!empty($_POST["dnum"])){//削除フォームが空でないとき
        $id = $_POST["dnum"];
        $sql = 'SELECT * FROM mission5_1 WHERE id=:id ';//データの取得
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する
        $results = $stmt->fetchAll(); 
        foreach ($results as $row){
        if($row['pass']==$_POST["dpass"]){//パスワードが一致したとき
	    $sql = 'DELETE FROM mission5_1 WHERE id=:id';
	    $stmt = $pdo->prepare($sql);
	    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        echo $id."番を削除しました<br>";
        }else {
            if(empty($_POST["dpass"])) echo "パスワードを入力してください<br>";//パスワード未入力
            else echo "パスワードが違います<br>";//パスワードが間違っている
           //$setdnum=$row['id'];//削除番号指定フォームに送信　※あとで修正
        }
    }
}
?>
<!--フォーム-->
<form action="" method="post">
        <input type="text" name="name" placeholder="名前" 
        value="<?php if(isset($editname)) echo $editname;?>"> <br>
        <input type="text" name="comment" placeholder="コメント" 
        value ="<?php if(isset($editcomment)) echo $editcomment;?>"> <br>
        <input type="password" name="pass" placeholder="パスワード"> <br>
        <input type="submit" name="submit"> <br>
        <input type="hidden" name="getnum" 
        value = "<?php if(isset($editnum)) echo $editnum;?>"> <br>
        <input type="number" name="dnum" placeholder="削除番号" 
        vallue="<?php if(isset($setdnum)) echo $setdnum;?>"> <br>
        <input type="password" name="dpass" placeholder="パスワード"> <br>
        <input type="submit" value="削除"> <br>
        <input type="nunber" name="enum" placeholder="編集番号"> <br>
        <input type="password" name="epass" placeholder="パスワード"> <br>
        <input type="submit" value="編集"> <br>
    </form>
    <?php
    //データレコードの抽出と表示
$sql = 'SELECT * FROM mission5_1';//データの取得
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
    //表示
    //$rowの中にはテーブルのカラム名が入る
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].',';
    echo $row['pass'].',';//※確認用、後で消す
    echo $row['date'].'<br>';
echo "<hr>";
}
    ?>
</body>
</html>