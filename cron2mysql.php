<?php

include 'lib.php';

$r = connredis();

$i = 0;

$sql = 'insert into post (postid,userid,username,create_time,content) values ';

while($r->lLen('global:store') && $i<1000){
    $postid = $r->rpop('global:store');
    $post = $r->hMGet('post:postid:'.$postid,['userid','username','time','content']);
    $sql.='("'.$postid.'","'.$post['userid'].'","'.$post['username'].'","'.$post['time'].'","'.$post['content'].'"),';
    $i++;
}

if($i<=0){
    echo '==========no job====';die;
}

$sql = substr($sql,0,-1);

echo $sql;

//链接mysql 并把旧微博入库
$conn = mysqli_connect('localhost','root','','test','3369');

mysqli_query($conn,'set names utf8');
$rs = mysqli_query($conn,$sql);

var_dump($rs);

echo 'ok';