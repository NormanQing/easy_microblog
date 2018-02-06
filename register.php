<?php
/**
 * 注册用户
   set user:userid:1:username zhangsan
   set user:userid:1:password 111111

   set user:username:zhangsan:userid 1

   userid 生成
   incr global:userid

   步骤
   1：接收参数 $_POST 判断参数用户名、密码参数合法
   2：连接redis 查询用户名，验证否存在
   3：写入redis
   4:登陆操作
 */
include 'header.php';
include 'lib.php';

if(isLogin()!== false){
	header('location: home.php');
	exit;
}

$username = P('username');
$password = P('password');
$password2 = P('password2');
if(!$username || !$password || !$password2){
	error('请输入完整的注册信息');
}
//密码一致性验证
if($password !== $password2){
	error('两次密码不一致！');
}

$r = connredis();

//验证用户名是否已经被注册
$hasUserid = $r->get('user:username:'.$username.':userid');
if($hasUserid){
	error('用户名已被注册，请重新选择');
}

//获取userid
$userid = $r->incr('global:userid');

$r->set('user:userid:'.$userid.':username',$username);
$r->set('user:userid:'.$userid.':password',$password);
$r->set('user:username:'.$username.':userid',$userid);

// 通过list 维护50个最新的userid
$r->lpush('newuser:link',$userid);
$r->ltrim('newuser:link',0,49);


