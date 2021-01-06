FROM php:7.3

MAINTAINER lixiang <16675112194@gmail>

# Version
ENV PHPREDIS_VERSION 4.0.1
ENV SWOOLE_VERSION 4.3.4
#ENV EASYSWOOLE_VERSION 3.x-dev PHPREDIS_VERSION 4.0.1 4.3.0 SWOOLE_VERSION 4.3.4 4.2.13 4.2.12

# Timezone
RUN /bin/cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime && echo 'Asia/Shanghai' > /etc/timezone

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

# 安装php扩展直接 docker-php-ext-install 扩展名就行
# Composer && PDO extension &&　Bcmath extension  curl -sS https://getcomposer.org/installer | php 或 php -r "readfile('https://getcomposer.org/installer');" | php
RUN php -r "readfile('https://getcomposer.org/installer');" | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer self-update --clean-backups \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install sockets \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install bcmath

# Redis extension && Swoole extension
RUN wget http://pecl.php.net/get/redis-${PHPREDIS_VERSION}.tgz -O /tmp/redis.tar.tgz \
    && pecl install /tmp/redis.tar.tgz \
    && rm -rf /tmp/redis.tar.tgz \
    && docker-php-ext-enable redis \
    && wget https://github.com/swoole/swoole-src/archive/v${SWOOLE_VERSION}.tar.gz -O swoole.tar.gz \
    && mkdir -p swoole \
    && tar -xf swoole.tar.gz -C swoole --strip-components=1 \
    && rm swoole.tar.gz \
    && ( \
    cd swoole \
    && phpize \
    && ./configure  --enable-sockets --enable-mysqlnd --enable-openssl --enable-http2 \
    && make -j$(nproc) \
    && make install \
    ) \
    && rm -r swoole \
    && docker-php-ext-enable swoole

WORKDIR /www

# Install project
#RUN cd /www && composer require easyswoole/easyswoole=${EASYSWOOLE_VERSION} && php vendor/bin/easyswoole install

#EXPOSE 9501 3366

ENTRYPOINT ["bash"]
#ENTRYPOINT ["php", "/var/www/code/easyswoole", "start"]
#docker run -p 8080:80 -v $(pwd):/www -itd --entrypoint="" --name swoole_develop1 --privileges=true 16675112194/swoft:official bash