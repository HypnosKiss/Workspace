FROM python:3.7

MAINTAINER wili-lixiang <wili.lixiang@gmail.com>

# Timezone
RUN /bin/cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime && echo 'Asia/Shanghai' > /etc/timezone

# 安装依赖包
RUN pip install pyOpenSSL && pip install lxml && pip install Twisted && pip install Scrapy

# Libs
RUN apt-get update \
    && apt-get install -y \
        curl \
        wget \
        git \
        zip \
        libz-dev \
        libssl-dev \
        libnghttp2-dev \
        libpcre3-dev \
    && apt-get clean \
    && apt-get autoremove

WORKDIR /www

#6800
EXPOSE 80

ENTRYPOINT ["bash"]

