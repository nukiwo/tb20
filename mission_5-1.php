<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <?php
    // DB接続設定
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    $sql = "CREATE TABLE IF NOT EXISTS mission5"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "password TEXT,"
	. "data TEXT"
	.");";
	$stmt = $pdo->query($sql);
    
	$pass_error=false;
    if(!empty($_POST["num_edit"])&&
    !empty($_POST["pass_edit"])&&!empty($_POST["submit3"]))
    {
        $id=$_POST["num_edit"];
    	$pass=$_POST["pass_edit"];
    	$sql = 'select * from mission5 where id=:id_edit and password=:pass_edit';
    	$stmt = $pdo->prepare($sql);
    	$stmt->bindParam(':id_edit', $id, PDO::PARAM_INT);
    	$stmt->bindParam(':pass_edit',$pass,PDO::PARAM_STR);
        $stmt->execute();
       	$result = $stmt->fetch(PDO::FETCH_ASSOC);
    	$id_edit=$result["id"];
        $name_edit=$result['name'];
        $comment_edit=$result['comment'];
        $pass_edit=$result['password'];
        if(empty($result)){
            $pass_error=true;
        }
    }
?>

<br>【　投稿フォーム　】<br>
    <form action=""method="post">
        <input type="text" name="name" value="<?php if(!empty($name_edit)){ 
            echo $name_edit;}?>" placeholder="名前">
        <input type="text" name="comment" value="<?php if(!empty($comment_edit)){ 
            echo $comment_edit;}?>" placeholder="コメント" >
        <input type="text" name="password" value="<?php if(!empty($pass_edit)){ 
            echo $pass_edit;}?>" placeholder="パスワード" >
        <input type="hidden" name="num" value="<?php if(!empty($id_edit)){ 
            echo $id_edit;}?>" >
        <input type="submit" name="submit1" value="送信">
        <br><br>
    
    【　削除フォーム　】<br>
        <input type="number" name="num_delete"
        value="" placeholder="削除対象番号">
        <input type="text" name="pass_delete"
        value="" placeholder="パスワード">
        <input type="submit" name="submit2" value="削除">
        <br>
        <br>
        
    【　編集フォーム　】<br>    
        <input type="number" name="num_edit"
        value="" placeholder="編集対象番号">
        <input type="text" name="pass_edit"
        value="" placeholder="パスワード">
        <input type="submit" name="submit3" value="編集">
    </form>

<?php
    if(!empty($_POST["name"])&& !empty($_POST["comment"])&&
        !empty($_POST["password"])&& !empty($_POST["submit1"]))
    {
        $name = $_POST["name"];
        $comment = $_POST["comment"];
    	$password = $_POST["password"];
    	$data = date("Y年m月d日 H時i分s秒");
    	if(empty($_POST["num"]))
        {
            $sql = 'SELECT max(id) as id FROM mission5';
            $stmt = $pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        	$id = $result["id"]+1;
        	$stmt = $pdo -> prepare("INSERT INTO 
            mission5 (id, name, comment, password, data) 
            VALUES (:id, :name, :comment, :password, :data)");
        }else
        {
            $id = $_POST["num"];
        	$sql = 'UPDATE mission5 SET name=:name,comment=:comment, 
            password=:password,data=:data WHERE id=:id';
        	$stmt = $pdo->prepare($sql);
        }
        $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
        $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
    	$stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
    	$stmt -> bindParam(':data', $data, PDO::PARAM_STR);
        $stmt -> execute();
    }
	
	if(!empty($_POST["num_delete"])&&
        !empty($_POST["pass_delete"])&& !empty($_POST["submit2"]))
    {
        $id_delete = $_POST["num_delete"];
    	$pass_delete=$_POST["pass_delete"];
        $sql = 'SELECT * FROM mission5 where id=:id_delete';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_delete',$id_delete, PDO::PARAM_INT);
    	$stmt->execute();
    	$result = $stmt->fetch(PDO::FETCH_ASSOC);
    	if($result["password"]!=$pass_delete){
            $pass_error=true;
        }
    	
    	$sql = 'delete from mission5 where id=:id_delete 
    	AND password=:pass_delete';
    	$stmt = $pdo->prepare($sql);
    	$stmt->bindParam(':id_delete', $id_delete, PDO::PARAM_INT);
    	$stmt->bindParam(':pass_delete',$pass_delete, PDO::PARAM_STR);
    	$stmt->execute();
    }
    
    echo "<br>";
    if(!empty($_POST["submit1"])){
        if(empty($_POST["name"])){
            echo "Error: Name is Empty.<br>";
        }else if(empty($_POST["comment"])){
            echo "Error: Comment is Empty.<br>";
        }else if(empty($_POST["password"])){
            echo "Error: Password is Empty.<br>";
        }
    }else if(!empty($_POST["submit2"])){
        if(empty($_POST["num_delete"])){
            echo "Error: Delete-Number is Empty.<br>";
        }else if(empty($_POST["pass_delete"])){
            echo "Error: Password is Empty.<br>";
        }
    }else if(!empty($_POST["submit3"])){
        if(empty($_POST["num_edit"])){
            echo "Error: Edit-Number is Empty.<br>";
        }else if(empty($_POST["pass_edit"])){
            echo "Error: Password is Empty.<br>";
        }
    }
        
    if($pass_error){
        echo "Error: Wrong ID or Password<br>";
    }
?>
<br>
    ------------------------------------------------<br>
    【　投稿一覧　】<br><br>
<?php
	$sql = 'SELECT * FROM mission5';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		echo $row['id'].' , ';
		echo $row['name'].' , ';
		echo $row['comment'].' , ';
		echo $row['data'].'<br>';
	echo "<hr>";
	}

?>

</body>
</html>