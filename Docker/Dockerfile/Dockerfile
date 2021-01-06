FROM php:7.3

MAINTAINER wili-lixiang <wili.lixiang@gmail.com>

# Version # Version ENV PHPREDIS_VERSION 4.0.0 4.3.0 | ENV SWOOLE_VERSION 4.3.5 4.4.0
ENV PHPREDIS_VERSION 4.3.0
ENV SWOOLE_VERSION 4.4.0

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

# Composer PDO extension Bcmath extension
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer self-update --clean-backups \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install sockets


# Redis extension
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
        && ./configure --enable-async-redis --enable-mysqlnd --enable-openssl --enable-http2 \
        && make -j$(nproc) \
        && make install \
    ) \
    && rm -r swoole \
    && docker-php-ext-enable swoole

#可以将本地文件夹或者其他container的文件夹挂载到container中。
#docker run -v $(pwd):/www -t aglio -i /www/input.apib -o /www/output.html
#VOLUME "./docker" "/www"
VOLUME /docker

WORKDIR /www

RUN composer init && composer install --no-plugins --no-scripts --no-dev \
    && composer dump-autoload -o \
    && composer clearcache

#EXPOSE 80

ENTRYPOINT ["bash"]
