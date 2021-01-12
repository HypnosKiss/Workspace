#输出重定向
```
exec('php backgroundScript.php 2>&1 &')  
shell_exec($Command.' 2>&1 > out.log')
php test.php >out.txt 2>&1 &  
```