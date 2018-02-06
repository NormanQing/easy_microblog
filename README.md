# easy_microblog
简易微博，基于redis

user表--对应的key规则

注册用户 user
incr global:userid 全局用户id
set user:userid:1:username zhangsan
set user:userid:1:password 111111

set user:username:zhangsan:userid 1


发微博 post
post:postid:3:time timestamp
post:postid:3:userid 5
post:postid:3:content 'this is my microblog'

incr global:postid 全局发微博的id

set post:postid:$postid:time timestamp
set post:postid:$postid:userid $userid
set post:postid:$postid:content $content

=====每人的微博前1000条存在redis,更旧的存入数据库

ps:每日的1000条以前的微博，都推到global:store链表中
用定时任务，去global:store中的前1000条入数据库


create table post(
    postid bigint primary key,
    userid int,
    username varchar(20) NOT NULL DEFAULT '',
    create_time int NOT NULL DEFAULT 0,
    content char(144)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微博表';


ab -c 50 -n 1000 -H 'Cookie:username=zhangsan; userid=1; authsecret=cJqbr3eDpy7xovYn' -p /www/blogpost -T 'application/x-www-form-urlencoded' http://easy_microblog.com/post.php?status=fromab
