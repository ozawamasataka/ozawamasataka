<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>簡易掲示板</title>
    <title>mission_5-1</title>
</head> 
 
<?php

//データベース接続
$dsn = 'mysql:dbname=データベース名;host=localhost';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//テーブルの作成
$sql = "CREATE TABLE IF NOT EXISTS mission5"
." ("
. "id INT AUTO_INCREMENT PRIMARY KEY,"
. "name char(32),"
. "comment TEXT,"
. "date TEXT,"
. "pass TEXT"
. ");";
$stmt = $pdo->query($sql);


///////////////////////////////////////////////////

//【投稿機能】

if(!empty($_POST["name"])&&!empty($_POST["comment"])&& !empty($_POST["pass"])){
 //echo "分岐に入りました";
     $name=$_POST["name"];
     $comment=$_POST["comment"];
     $date=date("Y/m/d H:i:s");
     $pass = $_POST["pass"];
    // echo $name;
    // echo $comment;
    // echo $date;
    //echo $pass;
    if(empty($_POST["editNo2"])){


//以下でデータベースにデータを入力(データレコードも挿入)
        $sql = $pdo -> prepare("INSERT INTO mission5 (name,comment,date,pass) VALUES (:name, :comment,:date,:pass)");
	    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);

        //$name=$_POST["name"];
        //$comment=$_POST["comment"];
        //$date=date("Y/m/d H:i:s");
    
        $sql -> execute();

    }else{
        //echo "分岐に入りました";
        $editNum = $_POST["editNo2"];
        $name=$_POST["name"];
        $comment=$_POST["comment"];
        $date=date("Y/m/d H:i:s");
        //echo $editNum;


        $sql = "SELECT * FROM mission5";//データベースからデータを取り出す
         $stmt = $pdo->query($sql);//クエリを実行
         $results = $stmt->fetchAll();//結果を配列で表示

         foreach ($results as $row){ 
             //編集番号とidが一致しているか
            if($editNum==$row['id']){
                //echo "分岐に入りました"; //確認済み

                $sql = 'UPDATE mission5 SET name=:name,comment=:comment,date=:date, pass=:pass WHERE id=:id';
	            $stmt = $pdo->prepare($sql);
                $stmt -> bindParam(':id',$editNum , PDO::PARAM_INT);
                $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
                $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
                $stmt -> bindParam(':pass', $postPass, PDO::PARAM_STR);

                
	            $stmt->execute();
            }        
        }
    }
}


///////////////////////////////////////////////////////////

//【削除機能】
if(!empty($_POST["deleteNo"])){

   //echo "削除の分岐に入りました";　//変な空白があったせいでエラーが起きてしまいました
    $deleteNo=$_POST["deleteNo"]; 
    $deletepass=$_POST["deletepass"];
    //echo $deleteNo;

    $sql = 'SELECT * FROM mission5';//データ抽出
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){ 
        //echo "分岐に入りました";
        //echo $deleteNo;

        if($deleteNo==$row['id']&&$deletepass==$row['pass']){//削除対象番号とidが一致しているとき
            $id=$deleteNo;//4-8
            $sql = 'delete from mission5 where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute(); 
        }
    }

} 

/////////////////////////////////////////////////////////////
//【編集機能】
//編集機能(既存のファイルに対して新規投稿として上書きをする)、動作確認済み
     //編集フォームに数字がある時に以下の処理を実行
if(isset($_POST["editNo"]) && !empty($_POST["editpass"])){

         $editNo=$_POST["editNo"];
         $editpass=!empty($_POST["editpass"]);

         $sql = "SELECT * FROM mission5";//データベースからデータを取り出す
         $stmt = $pdo->query($sql);//クエリを実行
         $results = $stmt->fetchAll();//結果を配列で表示
         foreach ($results as $row){ 
             if($editNo==$row['id']&&$_POST["editpass"]==$row['pass']){

                $editNumber= $row['id'];
                $editName = $row['name'];
                $editComment = $row['comment'];
             }
        }
            
}         


?>
 

 <!--投稿フォーム-->
 <form action="" method="post">
        <input type="text" name="name" placeholder="名前" value="<?php if(isset($editName)){ echo $editName;} ?>"><br>
        <input type="text" name="comment" placeholder="コメント" value="<?php if(isset($editComment)){ echo $editComment; } ?>"><br>
        <input type="text" name="editNo2" placeholder="" value="<?php if(isset($editNumber)){ echo $editNumber;} ?>">
        <input type="text" name="pass" placeholder="パスワード">
        <input type="submit" name="submit" value="送信"><br>   
        <br>
    </form>
    <!--削除フォーム-->
    <form action="" method="post">
         <input type="num" name="deleteNo" placeholder="削除対象番号">
         <input type="text" name="deletepass" placeholder="パスワード">
        <input type="submit" name="deleteBtn" value="削除">
    </form>
    <!--編集フォーム-->
     <form action="" method="post">
        <input type="num" name="editNo" placeholder="編集対象番号">
        <input type="text" name="editpass" placeholder="パスワード">
        <input type="submit" name="editBtn" value="編集">
    </form>   
 
<?php

/////////////////////////////////////////////////////////

//表示機能
$sql = 'SELECT * FROM mission5';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
    //$rowの中にはテーブルのカラム名が入る
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].',';
    echo $row['date'].'<br>';
echo "<hr>";
}

?>

</body>
</html>