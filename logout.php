<?php
include 'lib.php';
$uid = $_COOKIE['userid'];
setcookie('username','',-1);
setcookie('userid','',-1);
setcookie('authsecret','',-1);

$r = connredis();
$r->set('user:userid:'.$userid.':authsecret','');

header('location: index.php');
