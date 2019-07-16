#使用 Docker 构建 Yapi
#1、启动 MongoDB
docker run -d --name mongo-yapi mongo

#2、获取 Yapi 镜像，版本信息可在 阿里云镜像仓库 查看
docker pull registry.cn-hangzhou.aliyuncs.com/anoy/yapi

#3、初始化 Yapi 数据库索引及管理员账号
docker run -it --rm --link mongo-yapi:mongo --entrypoint npm --workdir /api/vendors registry.cn-hangzhou.aliyuncs.com/anoy/yapi run install-server

#自定义配置文件挂载到目录 /api/config.json，官方自定义配置文件 https://github.com/YMFE/yapi/blob/master/config_example.json

#4、启动 Yapi 服务
docker run -d --name yapi --link mongo-yapi:mongo --workdir /api/vendors -p 3000:3000 registry.cn-hangzhou.aliyuncs.com/anoy/yapi server/app.js

#使用 Yapi
#访问 http://localhost:3000   登录账号 admin@admin.com，密码 ymfe.org

#关闭 Yapi
docker stop yapi

#启动 Yapi
docker start yapi

#升级 Yapi
# 1、停止并删除旧版容器
docker rm -f yapi

# 2、获取最新镜像
docker pull registry.cn-hangzhou.aliyuncs.com/anoy/yapi

# 3、启动新容器
docker run -d --name yapi --link mongo-yapi:mongo --workdir /api/vendors -p 3000:3000 registry.cn-hangzhou.aliyuncs.com/anoy/yapi server/app.js

#构建任意版本 yapi 镜像 提示：以下所有文件均放在同一目录下
#1、编写 Dockerfile
FROM node:9.2-alpine as builder
RUN apk add --no-cache git python make openssl tar gcc
ADD yapi.tgz /home/
RUN mkdir /api && mv /home/package /api/vendors
RUN cd /api/vendors && \
    npm install --production --registry https://registry.npm.taobao.org
FROM node:9.2-alpine
MAINTAINER 545544032@qq.com
ENV TZ="Asia/Shanghai" HOME="/"
WORKDIR ${HOME}
COPY --from=builder /api/vendors /api/vendors
COPY config.json /api/
EXPOSE 3000
ENTRYPOINT ["node"]

#2、自定义配置文件 config.json
{
  "port": "3000",
  "adminAccount": "admin@admin.com",
  "db": {
    "servername": "mongo",
    "DATABASE": "yapi",
    "port": 27017
  }
}

#3、镜像构建脚本 build
echo -e "\033[32m download new package (version $1) \033[0m"

wget -O yapi.tgz http://registry.npm.taobao.org/yapi-vendor/download/yapi-vendor-$1.tgz

echo -e "\033[32m build new image \033[0m"

docker build -t registry.cn-hangzhou.aliyuncs.com/anoy/yapi .

#使脚本可执行： 
chmod a+x build

#4、本地构建镜像，版本列表 https://github.com/YMFE/yapi/releases
#示例： ./build 1.4.3
./build <Version> 
