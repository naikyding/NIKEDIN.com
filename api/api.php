<?php
header("Access-Control-Allow-Origin: *"); //這個必寫，否則報錯
session_start();
$db = new PDO('mysql:host=localhost;dbname=s1080417;charset=utf8','root','');
// $db = new PDO('mysql:host=localhost;dbname=s1080417;charset=utf8','s1080417','s1080417');
switch ($_GET['do']) {
  ////////////////////SLIDE
  case 'slide':
    $query = $db->query('SELECT * FROM slide WHERE 1');
    $slide;
    $num = 0;
    foreach($query->fetchAll() as $row){
      if($row['display'] != 0){
        $slide[] = array(
          'img' => $row['img'],
          'title' => $row['title'],
          'info' => $row['info']
        );
      }
    }
    echo $slide = json_encode($slide);
  break;
  case 'selectTable':
    $sql = $db->query('SELECT * FROM '.$_GET['table'].' WHERE 1 ;');
    $data;
    if(!empty($sql)){
      foreach($sql->fetchAll() as $row){
        $data[] = array(
          'img1' => $row['img1'],
          'img2' => $row['img2'],
          'title' => $row['title'],
          'tag' => $row['tag'],
          'price' => $row['sales_price']
        );
      }
      if(isset($data))echo $data = json_encode($data);
      else echo false;
    }else{
      echo false;
    }
  break;
  case 'productsSelect':
    // print_r($_GET);
    $sql = $db->prepare('SELECT * FROM '.$_GET['table'].' WHERE title =? ;');
    $sql->execute([$_GET['title']]);
    // print_r($sql->fetchAll());
    $data;
    foreach($sql->fetchAll() as $rows){
      $size = array(
        'XS' => $rows['XS'],
        'S' => $rows['S'],
        'M' => $rows['M'],
        'L' => $rows['L'],
        'XL' => $rows['XL'],
        'XXL' => $rows['XXL'],
        'XSnum' => $rows['XSnum'],
        'Snum' => $rows['Snum'],
        'Mnum' => $rows['Mnum'],
        'Lnum' => $rows['Lnum'],
        'XLnum' => $rows['XLnum'],
        'XXLnum' => $rows['XXLnum']
      );
      $img = array(
        'img1' => $rows['img1'],
        'img2' => $rows['img2'],
        'img3' => $rows['img3']
      );
      $data = array(
        'id' => $rows['id'],
        'title' => $rows['title'],
        'tag' => $rows['tag'],
        'sales_price' => $rows['sales_price'],
        'details' => $rows['details'],
        'color' => $rows['color'],
        'img' => $img,
        'care' => $rows['care'],
        'size' => $size
      );
    }
    if(empty($data)) echo false;
    else echo $data = json_encode($data);
  break;
  case 'accCheck':
    foreach ($_POST as $key => $val) {
      $obj = json_decode($key);
      $userId = $obj->{'userId'};
      $sql = $db->prepare('SELECT * FROM customer WHERE acc =? ;');
      $sql->execute([$userId]);
      if($sql->fetchAll()){
        echo true;
      }else{
        $sql = $db->prepare('SELECT * FROM admin WHERE acc=? ;');
        $sql->execute([$userId]); 
        if($sql->fetchAll()){
          echo true;
        }else{
          echo false;
        }
      }
    }
  break;
  case 'logIn':
    foreach ($_POST as $key => $val) {
      $obj = json_decode($key);
      $userId = $obj->{'acc'};
      $userPsw = $obj->{'psw'};
      $sql = $db->prepare('SELECT * FROM customer WHERE acc =? AND psw =? ;');
      $sql->execute([$userId, $userPsw]); 
      $data = $sql->fetchAll();
      if($data){
        foreach($data as $rows){
          $token = mt_rand();
          $sql = $db->query('UPDATE customer SET token = '.$token.' WHERE acc = "'.$rows['acc'].'" AND psw = '.$rows['psw'].' ;');
          $user = array(
            "userName" => $rows['acc'],
            "token" => $token
          );
          echo json_encode($user);
        }
      }else{
        $sql = $db->prepare('SELECT * FROM admin WHERE acc =? AND psw =? ;');
        $sql->execute([$userId, $userPsw]); 
        $data = $sql->fetchAll();
        if($data){
          foreach($data as $rows){
            $token = mt_rand();
            $sql = $db->query('UPDATE admin SET token = '.$token.' WHERE acc = "'.$rows['acc'].'" AND psw = '.$rows['psw'].' ;');
            $user = array(
              "userName" => $rows['acc'],
              "token" => $token
            );
            echo json_encode($user);
          }
        }else{
          echo false;
        }
      }
    }
  break;
  case 'rememberAcc':
    foreach($_POST as $key => $val) {
      $sql = $db->prepare('SELECT * FROM customer WHERE token =? ;');
      $sql->execute([$key]);
      $data = $sql->fetch();
      if($data) echo $data['acc'];
      else return false;
    }
  break;
  case 'SET_listToSession':
    // print_r($_POST);
    // print_r($_GET['user']);
    foreach($_POST as $key => $val) {
      $data = json_decode($key, true);
      foreach($data as $key => $val) {
        $_SESSION['customer'][$_GET['user']] = [
          'orderCart' => $val
        ];
      }
      print_r($_SESSION);
    }
  break;
  case 'GET_listToSession':
    if(!empty($_SESSION['customer'][$_GET['user']])){
      echo json_encode($_SESSION['customer'][$_GET['user']]['orderCart']);
    }else{
      echo false;
    }
  break;
  case 'GET_slide':
    $rows = $db->query('SELECT * FROM slide WHERE 1 ;');
    $data;
    foreach($rows as $row) {
      $data[] = [
        'dsp' => (int)$row['display'],
        'id' => (int)$row['id'],
        'img' => $row['img'],
        'title' => $row['title'],
        'info' => $row['info']
      ];
    }
    echo $data = json_encode($data);
  break;
  case 'DELETE_slide':
    foreach($_POST as $id => $val) {
      $sql = $db->prepare('DELETE FROM slide WHERE id =? ;');
      $res = $sql->execute([$id]);
      echo $res;
    }
  break;
  case 'PATCH_slide':
    $id = $_POST['id'];
    $dsp = !isset($_POST['dsp'][$id])?0:1;
    $sql = $db->prepare('UPDATE slide SET title =?, info=?, display=? WHERE id=? ;');
    $res = $sql->execute([$_POST['title'][$id], $_POST['info'][$id], $dsp, $id]);
    echo $res;
  break;
  case '':
    $rows = $db->query('SELECT * FROM eventMail WHERE 1 ;');
    
  break;
  case 'POST_slide':
    // print_r($_POST);
    // print_r($_FILES);
    $index = 'id';
    $value = 'NULL';
    foreach($_POST as $key => $val){
      $index .= ', '.$key;
      $value .= ', "'.$val.'"';
    }
    if($_FILES['img']['error'] == 0){
      $index .= ', img';
      $value .= ', "'.$_FILES['img']['name'].'"';
      $src = $_FILES['img']['tmp_name'];
      copy($src,'../images/slide/'.$_FILES['img']['name']);
    }
    $sql = 'INSERT INTO slide ('.$index.') VALUES ('.$value.') ;';
    $res = $db->query($sql);
    echo $res = ($res)?true:false;
  break;
  default:
    # code...
  break;
}