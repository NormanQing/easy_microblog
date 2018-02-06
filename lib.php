<?php

//函数库封装

function P($key){
	return isset($_POST[$key]) ? $_POST[$key] : false;
}

function G($key){
	return isset($_GET[$key]) ? $_GET[$key] : false;
}

//报错函数
function error($msg){
	echo '<div>'.$msg.'</div>';
	include './footer.php';
	exit;
}

function connredis(){
	static $r = null;
	if($r !== null){
		return $r;
	}
	$r = new redis();
	$r->connect('localhost');
	return $r;
}

//判断用户是否登录
function isLogin(){
	if(!isset($_COOKIE['userid']) || !isset($_COOKIE['username']) || !isset($_COOKIE['authsecret'])){
		return false;
	}
	 $r = connredis();

	$authsecret = $r->get('user:userid:'.$_COOKIE['userid'].':authsecret');
	if($_COOKIE['authsecret'] != $authsecret){
		return false;
	}
	return [
		'userid'=>$_COOKIE['userid'],
		'username'=>$_COOKIE['username']
	];
}

function randsecret(){
	$str = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	return substr(str_shuffle($str),0,16);
}

//格式化时间
function formatTime($time){
	$sec = time() - $time;
	if($sec>=86400){
		return floor($sec/86400).'天前';
	}elseif($sec >= 3600){
		return floor($sec/3600).'小时前';
	}elseif($sec>=60){
		return floor($sec/60).'分钟前';
	}else{
		return $sec.'秒前';
	}
}
