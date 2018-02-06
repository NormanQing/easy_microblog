<?php
include 'header.php';
include 'lib.php';
$user = isLogin();
if(false === $user){
	header('location: index.php');
	exit;
}
$r = connredis();
/*v1.0 
//自己接受的微博
//取出自己发的和粉丝推过来的信息
$r->ltrim('recivepost:'.$user['userid'],0,49);
//v1.0
//$newpost = $r->sort('recivepost:'.$user['userid'],['sort'=>'desc','get'=>'post:postid:*:content']);

//v2.0 用hash结构存储微博
$newpost = $r->sort('recivepost:'.$user['userid'],['sort'=>'desc']);
//var_dump($newpost);die;
$postlist = [];
foreach($newpost as $key=>$postid){
	
$postlist[$key] = $r->hmget('post:postid:'.$postid,['userid','content','time']);

}
*/
/*
   v3.0
 */
//我关注的人
$star = $r->smembers('following:'.$user['userid']);
$star[] = $user['userid'];

$lastpull = $r->get('lastpull:userid:'.$user['userid']);

if(!$lastpull){
	$lastpull = 0;
}
//拉取数据
$latest = [];
foreach($star as $s){
    $latest = array_merge($latest,$r->zrangebyscore('starpost:userid:'.$s,$lastpull+1,1<<32-1));

}
//print_r($latest);
//更新lastpull

sort($latest,SORT_NUMERIC);

//如果非空才去更新
if(!empty($latest)){
    $r->set('lastpull:userid:'.$user['userid'],end($latest));
}




//循环把latet放到自己（主页）应该收取的微博链表离
foreach($latest as $l){
    $r->lPush('receivepost:'.$user['userid'],$l);
}

//保持个人主页最多收取1000条最新微博

$r->lTrim('receivepost:'.$user['userid'],0,999);



//
$newpost = $r->sort('receivepost:'.$user['userid'],['sort'=>'desc']);

$postlist = [];
foreach($newpost as $key=>$postid){

    $postlist[$key] = $r->hmget('post:postid:'.$postid,['userid','content','time','username']);

}





//die;
//计算几个粉丝，几个关注
//计算集合元素个数

$myfans = $r->sCard('follower:'.$user['userid']);//我的粉丝数
$mystar = $r->sCard('following:'.$user['userid']);//我关注的人数


?>
<div id="navbar">
<a href="index.php">主页</a>
| <a href="timeline.php">热点</a>
| <a href="logout.php">退出</a>
</div>
</div>
<div id="postform">
<form method="POST" action="post.php">
<?=$user['username']?>, 有啥感想?
<br>
<table>
<tr><td><textarea cols="70" rows="3" name="status"></textarea></td></tr>
<tr><td align="right"><input type="submit" name="doit" value="Update"></td></tr>
</table>
</form>
<div id="homeinfobox">
<?=$myfans?> 粉丝<br>
<?=$mystar?> 关注<br>
</div>
</div>
<?php /*
		 //======v1.0======
		 foreach($newpost as $post){?>
<div class="post">
<a class="username" href="profile.php?u=test">test</a> <?=$post?><br>
<i>11 分钟前 通过 web发布</i>
</div>
<?php } */?>
<?php //======v2.0======= ?>
<?php foreach($postlist as $post){?>
<div class="post">
<a class="username" href="profile.php?u=<?=$post['username']?>"><?=$post['username']?></a> <?=$post['content']?><br>
<i><?=formatTime($post['time'])?> 通过 web发布</i>

</div>
<?php }?>
<?php //======v2.0=======?>
<?php include 'footer.php';?>
