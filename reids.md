
web知识-平时工作生活中的总结
博客园首页新随笔联系订阅管理
随笔 - 85  文章 - 0  评论 - 48
windows下安装Redis并部署成服务
windows下安装Redis并部署成服务
Redis 是一个开源（BSD许可）的，内存中的数据结构存储系统，它可以用作数据库、缓存和消息中间件。

一：下载
下载地址：
windows版本：
    https://github.com/MSOpenTech/redis/releases
Linux版本：
    官网下载：
        http://www.redis.cn/
    git下载
        https://github.com/antirez/redis/releases
我们现在讨论的是windows下的安装部署，目前windows下最新版本是：3.2.100。
下载地址，提供多种下载内容，
Redis-x64-3.2.100.msi是在windows下，最简单的安装文件，方便，直接会将Redis写入windows服务。
Redis-x64-3.2.100.zip是需要解压安装的，接下来讨论的是这种。
Source code (zip) 源码的zip压缩版
Source code (tar.gz) 源码的tar.gz压缩版
图片1

二：安装
解压安装
将下载的Redis-x64-3.2.100.zip 解压到某个地址。
解压之后存放的地址

启动命令
通过cmd指定到该redis目录。
使用命令：redis-server.exe 启动服务
启动服务
出现这种效果，表明启动服务成功。

启动另一个cmd，在该redis目录下，使用命令：redis-cli.exe 启动客户端,连接服务器
启动客户端
出现这种效果，表明启动客户度成功。

三：部署
由于上面虽然启动了redis服务，但是，只要一关闭cmd窗口，redis服务就关闭了。所以，把redis设置为一个windows服务。
安装之前，windows服务是不包含redis服务的 安装之前的服务

安装为windows服务
安装命令: redis-server.exe --service-install redis.windows.conf 使用命令，安装成功，如图所以： 安装命令
最后的参数 --loglevel verbose表示记录日志等级
安装之后，windows目前的服务列表 
安装之后的服务

常用的redis服务命令。
卸载服务：redis-server --service-uninstall

开启服务：redis-server --service-start

停止服务：redis-server --service-stop

重命名服务：redis-server --service-name name

重命名服务，需要写在前三个参数之后，例如：

The following would install and start three separate instances of Redis as a service:   
以下将会安装并启动三个不同的Redis实例作服务：

redis-server --service-install --service-name redisService1 --port 10001

redis-server --service-start --service-name redisService1

redis-server --service-install --service-name redisService2 --port 10002

redis-server --service-start --service-name redisService2

redis-server --service-install --service-name redisService3 --port 10003

redis-server --service-start --service-name redisService3
四：测试
启动服务
redis-server --service-start

客户端
命令：

精简模式：
redis-cli.exe
指定模式：
redis-cli.exe -h 127.0.0.1 -p 6379 -a requirepass
(-h 服务器地址  -p 指定端口号 -a 连接数据库的密码[可以在redis.windows.conf中配置]，默认无密码)
测试读写数据
测试数据读写

安装测试成功。

 

五：Redis桌面管理工具
推荐使用的桌面管理工具：
Redis Desktop Manager

下载地址：
https://redisdesktop.com/download
感谢您的认真阅读，更多内容请查看:
出处：http://www.cnblogs.com/weiqinl
个人主页http://weiqinl.com
github: weiqinl
简书:weiqinl
您的留言讨论是对博主最大的支持！
本文版权归作者所有，欢迎转载，但未经作者同意必须保留此段声明，且在文章页面明显位置给出原文连接，否则保留追究法律责任的权利。
好文要顶 关注我 收藏该文  
weiqinl
关注 - 15
粉丝 - 36
+加关注
7
« 上一篇： Docker实战--部署简单nodejs应用
» 下一篇： js判断字符串是否全为空(使用trim函数/正则表达式)
posted @ 2017-03-02 14:19  weiqinl  阅读(12204)  评论(1)  编辑  收藏

评论列表
  #1楼2018-06-15 14:26 monkey's
写的详情，且准确!
支持(0) 反对(0)
刷新评论刷新页面返回顶部
注册用户登录后才能发表评论，请 登录 或 注册， 访问 网站首页。
【推荐】超50万行VC++源码: 大型组态工控、电力仿真CAD与GIS源码库

相关博文：
· windows下安装Redis并部署成服务
· windows下redis安装
· Windows下安装Redis
· Redis在windows下安装与配置
· Redis在windows下安装过程
» 更多推荐...

最新 IT 新闻:
· 关于黑洞：爱因斯坦广义相对论这回或错了 对的是霍金
· 云计算+大数据+AI增强规模效益，「UCloud」将重点瞄准传统行业客户
· 印度政府为量子计算研发投入78亿元 欲缩小与中美差距
· 今年1月：内存、闪存芯片上游成交价持续上涨
· AI 要更聪明了？Deepmind 提出新架构：可实现更高级别推理
» 更多新闻...
公告
记录开始时间：2017-07-01 weiqinl
个人博客https://weiqinl.com
github: weiqinl 简书:weiqinlFork me on GitHub
昵称： weiqinl
园龄： 6年4个月
粉丝： 36
关注： 15
+加关注
<	2020年2月	>
日	一	二	三	四	五	六
26	27	28	29	30	31	1
2	3	4	5	6	7	8
9	10	11	12	13	14	15
16	17	18	19	20	21	22
23	24	25	26	27	28	29
1	2	3	4	5	6	7
搜索
 
 
最新随笔
1.git reset 之后切换到原来的commit
2.nrm安装和使用--管理你的npm源
3.异步请求xhr、ajax、axios与fetch的区别比较
4.js回文数的四种判断方法
5.js获取数组中的最大值/最小值
6.Reactjs事件处理的三种写法
7.Nuxt.js国际化vue-i18n的搭配使用
8.js中ASCII码和字符互相转换的方法
9.webpack打包优化之外部扩展externals的实际应用
10.JavaScript模块化CommonJS/AMD/CMD/UMD/ES6Module的区别
我的标签
javascript(31)css(11)ibatis.net(9)vuejs(9)webpack(8)开发工具(6)html(6)数据结构(5)算法(5)vue(4)更多
积分与排名
积分 - 121247
排名 - 4761
随笔分类 (90)
CSS(12)
Docker(2)
HTML(8)
JAVA(1)
Javascript(30)
Nodejs(4)
Reactjs(1)
Tools(3)
Vuejs(11)
webpack(8)
ZeroMQ(5)
数据结构与算法(5)
随笔档案 (85)
2020年1月(1)
2019年8月(1)
2019年7月(1)
2019年6月(1)
2019年5月(1)
2019年4月(1)
2019年3月(2)
2018年11月(2)
2018年10月(3)
2018年9月(4)
2018年8月(5)
2018年7月(3)
2018年6月(3)
2018年5月(2)
2018年4月(1)
2018年3月(1)
2018年1月(2)
2017年12月(1)
2017年11月(1)
2017年10月(3)
2017年9月(2)
2017年8月(4)
2017年6月(4)
2017年5月(8)
2017年4月(2)
2017年3月(4)
2017年2月(2)
2017年1月(3)
2016年6月(1)
2016年5月(7)
2016年4月(1)
2016年1月(1)
2015年12月(1)
2015年11月(4)
2015年9月(1)
2015年8月(1)
相册 (1)
记录(1)
最新评论
1. Re:js属性对象的hasOwnProperty方法
@ 果感引用原文链接： 参考链接：英文版：...
--weiqinl
2. Re:js属性对象的hasOwnProperty方法
原文链接：
--果感
3. Re:基于vue2.0前端组件库element中 el-form表单 自定义验证填坑
6666666666
--github.com/starRTC
4. Re:js属性对象的hasOwnProperty方法
very good
--追枫博客园
5. Re:Windows上安装nodejs版本管理器nvm
非常感谢你的文章
--小鱼板
阅读排行榜
1. 基于vue2.0前端组件库element中 el-form表单 自定义验证填坑(100914)
2. js属性对象的hasOwnProperty方法(58753)
3. 解决问题SyntaxError: Unexpected token import(37841)
4. js将某个值转换为String字符串类型或转换为Number数字类型(13706)
5. css固定div头部 滚动条滚动内容(12978)
评论排行榜
1. 基于vue2.0前端组件库element中 el-form表单 自定义验证填坑(17)
2. NetMQ（四）： 推拉模式 Push-Pull(7)
3. HTML5原生拖拽/拖放(drag & drop)详解(6)
4. js属性对象的hasOwnProperty方法(4)
5. Javascript数据结构与算法--栈的实现与用法(3)
推荐排行榜
1. js属性对象的hasOwnProperty方法(37)
2. 基于vue2.0前端组件库element中 el-form表单 自定义验证填坑(21)
3. HTML5原生拖拽/拖放(drag & drop)详解(13)
4. windows下安装Redis并部署成服务(7)
5. 基于ElementUI的网站换主题的一些思考与实现(5)
Copyright © 2020 weiqinl
Powered by .NET Core 3.1.1 on Linux
