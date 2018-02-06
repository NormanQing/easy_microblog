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
