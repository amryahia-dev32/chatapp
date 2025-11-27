<?php
require 'config.php';
$user_id=$_SESSION['user_id']??0;
if(!$user_id){echo json_encode(['error'=>'no_login']);exit;}
$chat_id=(int)$_POST['chat_id'];
$dir='uploads/';
if(!is_dir($dir)) mkdir($dir);
$ext=pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
$type=in_array($ext,['mp3','ogg','wav'])?'audio':'file';
$path=$dir.time().'_'.$user_id.'.'.$ext;
move_uploaded_file($_FILES['file']['tmp_name'],$path);
$mysqli->query("INSERT INTO messages (chat_id,sender_id,content,type) VALUES ($chat_id,$user_id,'$path','$type')");
echo json_encode(['ok'=>true,'path'=>$path,'type'=>$type]);
?>
