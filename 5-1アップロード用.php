<?php  
    //データベース接続
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //テーブルの生成
    $sql="CREATE TABLE IF NOT EXISTS mission_5"
    ."("
    ."id INT AUTO_INCREMENT PRIMARY KEY,"
    ."name char(32),"
    ."comment TEXT,"
    ."date TEXT,"
    ."pass TEXT"
    .");";
    $stmt=$pdo->query($sql);
?>

<?php $h_num="";//編集識別機能
      $editname="";
      $editcomment="";
      $var=0;
      $nvar=0;
      $row='';
      if(!empty($_POST["enum"])){
          //enumが空じゃない時に以下を実行
          $id=$_POST["enum"];
          $id=(int)$id;
          $pass=$_POST["epass"];
          $sql='SELECT id, name, comment,pass FROM mission_5 WHERE id=:id';
          //データベースのidと$idが一致するときにデータベースからid,name,comment,passを引っ張ってくる
          $stmt=$pdo->prepare($sql);
          //sqlの準備
          $stmt->bindParam( ':id', $id, PDO::PARAM_INT);
          //カラムと変数の結び付け（バインド）
          $stmt->execute();
          //sqlを実行
          $result=$stmt->fetchAll();
          //resultに実行した結果を入れる
          foreach($result as $row){
              //実行結果が配列になっているので、それぞれ分けて表示
                 if($row["pass"]==$pass){
                    //パスワードが正しいか判別
                    $h_num=$row['id'];
                    $editname=$row['name'];
                    $editcomment=$row['comment'];
                    //フォームに表示させるために変数に入れる
                    $var=1;//結果の表示に用いる（投稿番号＆パスワードが等しい時）
                    break;//ループする必要ないのですぐ抜ける
                 }else{
                    $var=2;//結果の表示に用いる（投稿番号〇だが、パスワードが違う時）
                 }
              
          }
          if(empty($row['id'])){//投稿番号が存在しない時
              $nvar=1;
          }
          if(empty($pass)){
              $var=3;//結果の表示に用いる（投稿番号〇だが、パスワードが入力されていない時）
          }
      }elseif(!empty($_POST["epass"])){
          $nvar=2;
      }
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission_5</title>
    </head>
    <body>
        <form method="post" action="">
          【投稿欄】<br>
          <input type="text" name="name" placeholder="名前"
          value="<?php if(!empty($editname)){echo $editname;} ?>"><br>
          <input type="text" name="comment" placeholder="コメント"
          value="<?php if(!empty($editcomment)){echo $editcomment;} ?>"><br>
          <input type="hidden" name="h_num" value="<?php if(!empty($h_num)){echo $h_num;} ?>">
          <input type="text" name="pass" placeholder="パスワード"><br>
          <input type="submit"  value="送信"><br><br>
          【削除欄】<br>
          <input type="number" name="dnum" placeholder="削除番号"><br>
          <input type="text" name="dpass" placeholder="パスワード"><br>
          <input type="submit" name="delete" value="削除"><br><br>
          【編集欄】<br>
          <input type="number" name="enum" placeholder="編集番号"><br>
          <input type="text" name="epass" placeholder="パスワード"><br>
          <input type="submit" name="edit" value="編集"><br><br>
         </form>
         【投稿内容】
          <hr>
    </body>
</html>

<?php 
      if (!empty($_POST["name"])) {//編集実行機能＆通常投稿機能
           if(!empty($_POST["comment"])){
             if(!empty($_POST["pass"])){
               if(!empty($_POST["h_num"])){//編集実行機能
                  $id=$_POST["h_num"];
                  $name=$_POST["name"];
                  $comment=$_POST["comment"];
                  $pass=$_POST["pass"];
                  $sql='UPDATE mission_5 SET name=:name,comment=:comment,pass=:pass WHERE id=:id';
                  //idが等しいとき、データベースを更新。今回はパスワードも更新（再設定）する
                  $stmt=$pdo -> prepare($sql);
                  //sqlを準備
                  $stmt->bindParam(':id',$id, PDO::PARAM_INT);
                  $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                  $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                  $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                  //カラムと変数を結び付け（バインド）
                  $stmt->execute();
                  //sql（編集）を実行
                  echo "編集が完了しました<br><br>";//編集実行機能終了
                  
                  
               }else{//通常投稿機能
                  $sql = $pdo->prepare("INSERT INTO mission_5 (id,name,comment,date,pass)
                  VALUES (:id, :name, :comment, :date, :pass)");
                  //データベースへの挿入（書込み）準備
                  $sql -> bindParam(':id', $id, PDO::PARAM_INT);
                  $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                  $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                  $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                  $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                  //カラムと変数結び付け（バインド）
                  $name=$_POST["name"];
                  $comment=$_POST["comment"];
                  $date=date("Y/m/d H:i:s");
                  $pass=$_POST["pass"];
                  //変数の定義
                  $sql -> execute();
                  //変数からデータベースへデータを入力（sqlの実行）
                  echo "投稿完了しました<br><br>";
               }
             }else{
                 echo"パスワードを入力してください<br><br>";
             }
               
           }else{
               echo "エラー：コメントを入力してください<br><br>";
           }
     }elseif(empty($_POST["dnum"]) && empty($_POST["enum"]) &&empty($_POST["dpass"]) &&empty($_POST["epass"])){
         echo "入力してください<br><br>";
     }//通常投稿↑
     //編集実行機能＆通常投稿機能終了
           
                  
                   
      if(!empty($_POST["dnum"])){//削除機能
          if(!empty($_POST["dpass"])){
                $id=$_POST["dnum"];
                $pass=$_POST["dpass"];
                $sql='SELECT id, pass FROM mission_5 WHERE id=:id';
                //まずパスワードがあってるか確認のためにデータベースからパスワードをとってくる
                $stmt=$pdo->prepare($sql);
                $stmt->bindParam( ':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $result=$stmt->fetchAll();
                foreach($result as $row){
                      if($row["pass"]==$pass){
                          //データベースからとってきたパスワードと入力されたパスワードが等しかったら削除実行
                          $sql= 'delete from mission_5 where id=:id AND pass=:pass';//delete機能
                          $stmt=$pdo->prepare($sql);
                          $stmt->bindParam(':id',$id, PDO::PARAM_INT);
                          $stmt->bindParam(':pass',$pass, PDO::PARAM_STR);
                          $stmt->execute();
                          echo "削除が完了しました<br><br>";
                          
                          break;
                      }else{//パスワードが正しくなかったら削除しない
                          echo "パスワードが正しくありません<br><br>";
                      }
                }
                if(empty($row['id'])){//投稿番号が存在しない時
                       echo "投稿番号が存在しません<br><br>";
                }
          }else{
              echo "エラー：パスワードを入力してください<br><br>";
          }
      }//削除機能終了
      
      
      
      $sql='SELECT * FROM mission_5';//テーブル内容のブラウザ表示
      $stmt=$pdo->query($sql);
      $result=$stmt->fetchAll();
      foreach($result as $row){
              echo $row['id'].' ';
              echo $row['name'].' ';
              echo '「'.$row['comment'].'」'.' ';
              echo $row['date'].'<br>';
      }echo "<hr>";
?>