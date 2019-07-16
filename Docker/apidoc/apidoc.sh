#!/bin/bash
#file:docker_install.sh

function docker_install()
{
	echo "检查Docker......"
	docker -v
    if [ $? -eq  0 ]; then
        echo "检查到Docker已安装!"
    else
    	echo "安装docker环境..."
        curl -sSL https://get.daocloud.io/docker | sh
        echo "安装docker环境...安装完成!"
    fi
    # 创建公用网络==bridge模式
    #docker network create share_network
    #docker network create  --subnet=172.10.0.0/16  mynetwork
    docker run --rm -v D:\phpstudy\PHPTutorial\WWW\SVN\NuoManDiShangCheng:/www --name="apidoc" -it 16675112194/swoole:apidoc_v1 /www/apidoc -i /www/api/modules/v1/controllers  -o /www/output/
}

# 执行函数
docker_install

#官方提供shell脚本安装
#curl -fsSL https://get.docker.com -o get-docker.sh
#sudo sh get-docker.sh

