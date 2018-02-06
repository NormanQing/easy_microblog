<?php

/**
  登陆
  步骤
  1:接收参数，判断合法性，完整性
  2：查询用户名是否存在
  3：查询密码是否匹配
  4：设置cookie
 */

include 'lib.php';
include 'header.php';

if(isLogin() !==false){
	header('location: home.php');
	exit;
}


$username = P('username');
$password = P('password');

if(!$username || !$password){
	error('请输入用户名或者密码');
}

$r = connredis();

//获取用户信息
$userid = $r->get('user:username:'.$username.':userid');

if(!$userid){
	error('用户名错误！');
}
$realpass = $r->get('user:userid:'.$userid.':password');
var_dump($password.'---'.$realpass);
if($password != $realpass){
	error('密码错误！');
}

//设置cookie 登陆成功

$authsecret = randsecret();
$r->set('user:userid:'.$userid.':authsecret',$authsecret);

setcookie('username',$username);
setcookie('userid',$userid);
setcookie('authsecret',$authsecret);

header('location: home.php');


