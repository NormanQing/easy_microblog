<?php
include 'lib.php';
include 'header.php';

/** 
 incr global:postid 全局发微博的id 

 v1.0
 set post:postid:$postid:time timestamp
 set post:postid:$postid:userid $userid
 set post:postid:$postid:content $content

 v2.0 数据模型修改为hash格式存储

1：判单是否登录
2：接收post参数内容
3：set redis
 */
$content = P('status');
if(!$content){
	error('请输入内容');
}
//登陆
$user = isLogin();
if(false === $user){
	header('location: index.php');
	exit;
}

$r = connredis();
$postid = $r->incr('global:postid');
//
//v1.0 数据格式
/*$r->set('post:postid:'.$postid.':userid',$user['userid']);
$r->set('post:postid:'.$postid.':time',time());
$r->set('post:postid:'.$postid.':content',$content);
*/

/*v1.0
//把微博推给自己的粉丝
$fans = $r->smembers('follower:'.$user['userid']);//我的粉丝
$fans[] = $user['userid'];//讲自己也加入到推送中
foreach($fans as $fansid){
	$r->lpush('recivepost:'.$fansid,$postid);
}
*/
/**v2.0====不推了
 */
//v2.0 数据格式 hash
$r->hmset('post:postid:'.$postid,['userid'=>$user['userid'],'username'=>$user['username'],'content'=>$content,'time'=>time()]);

/**
v2.0 ======= 不进行推送的方式实现
把自己发的微博维护在一个有序集合中,只要前20个
 */
$r->zadd('starpost:userid:'.$user['userid'],$postid,$postid);
if($r->zcard('starpost:userid:'.$user['userid']) > 20){
    $r->zremrangbyrank('starpost:userid:'.$user['userid'],0,0);//把最旧的删掉
}

header('location: home.php');

include 'footer.php';
