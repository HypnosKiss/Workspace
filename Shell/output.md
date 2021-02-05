#输出重定向
```
exec('php backgroundScript.php 2>&1 &')  
shell_exec($Command.' 2>&1 > out.log')
php test.php >out.txt 2>&1 &
//screen -L -t window1 -dmS test的意思是启动test会话，test会话的窗口名称为window1。屏幕日志记录在/tmp/screenlog_window1.log。  
```