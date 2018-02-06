<?php 
include 'lib.php';
include 'header.php';

$user = isLogin();
if(false === $user){
	header('location: index.php');
	exit;
}
/**
 思路
 每人有自己的粉丝记录 set
 每人有自己的关注记录 set

 aid 关注bid
 发生

following:aid(big)
follower:bid(aid)
 */
/**
1:获取用户名
2:查询id
3:根据id,是否在我的following 集合中

 */
$u = G('u');
$r = connredis();

//当前页面用户 参数的用户
$prouid = $r->get('user:username:'.$u.':userid');
if(!$prouid){
	error('非法用户');
}

$isf = $r->sismember('following:'.$user['userid'],$prouid);
$isfstatus = $isf ? '0' :'1';
$isfword = $isf ? '取消关注': '关注Ta';




?>
<div id="navbar">
<a href="index.php">主页</a>
| <a href="timeline.php">热点</a>
| <a href="logout.php">退出</a>
</div>
</div>
<h2 class="username"><?=$u?></h2>
<a href="follow.php?uid=<?=$prouid?>&f=<?=$isfstatus?>" class="button"><?=$isfword?></a>

<div class="post">
<a class="username" href="profile.php?u=test">test</a> 
world<br>
<i>11 分钟前 通过 web发布</i>
</div>

<div class="post">
<a class="username" href="profile.php?u=test">test</a>
hello<br>
<i>22 分钟前 通过 web发布</i>
</div>
<?php include 'footer.php';?>
