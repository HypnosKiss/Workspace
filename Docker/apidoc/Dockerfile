FROM node:latest

MAINTAINER "wili-lixiang" <wili-lixiang@gmail.com>

RUN npm install apidoc -g

ENTRYPOINT ["apidoc"]
CMD ["--help"]

#apidoc -i api/modules/v1/controllers -o output/
#docker run --rm -v D:\phpstudy\PHPTutorial\WWW\SVN\NuoManDiShangCheng:/www --name="apidoc" -it 16675112194/swoole:apidoc_v1 /www/apidoc -i /www/api/modules/v1/controllers  -o /www/output/