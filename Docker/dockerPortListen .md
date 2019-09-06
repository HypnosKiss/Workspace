#我遇到过同样的问题。由于某种原因Windows保留端口2375：
netsh interface ipv4 show excludedportrange protocol=tcp
#如果您看到端口范围之一包含端口2375，则您遇到相同的问题。
#禁用Hyper-V并重新启动：
dism.exe /Online /Disable-Feature:Microsoft-Hyper-V
#然后保留端口2375：
netsh int ipv4 add excludedportrange protocol=tcp startport=2375 numberofports=1
#启用Hyper-V并重新启动：
dism.exe /Online /Enable-Feature:Microsoft-Hyper-V /All
#查看端口
netstat -a -o -n | Select-String -Pattern 2375
netstat -an | findstr 2375