<?php
include 'lib.php';
include 'header.php';

/**
思路
每人有自己的粉丝记录 set
每人有自己的关注记录 set
 
aid 关注bid
发生
following:aid(big)
follower:bid(aid)
*/
/*
1:获取用户名
2:查询id
3:根据id,是否在我的following 集合中
		   
*/


$user = isLogin();

if(false === $user){
	header('location: index.php');
	exit;
}
$uid = G('uid');
$f = G('f');

/**
1:uid f是否合法
2：uid 是否是自己
 */

$r = connredis();
if(1 == $f){
	$r->sadd('following:'.$user['userid'],$uid);
	$r->sadd('follower:'.$uid,$user['userid']);
}else{
	$r->srem('following:'.$user['userid'],$uid);
	$r->srem('following:'.$user['userid'],$uid);
}
$uname = $r->get('user:userid:'.$uid.':username');

header('location: profile.php?u='.$uname);
