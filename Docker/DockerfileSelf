FROM centos:7.4.1708

RUN yum -y install gcc gcc++ gcc-c++ wget make libxml2 libxml2-devel openssl openssl-devel curl curl-devel libpng libjpeg libjpeg-devel libpng-devel freetype freetype-devel  bzip2 bzip2-devel bison autoconf readline-devel libedit-devel gmp  gmp-devel

COPY php-7.1.9.tar.gz /usr/src/

COPY php.sh /etc/profile.d/

COPY ld.conf /etc/ld.so.conf.d/

COPY composer.phar swoole-src-2.1.3.tar.gz v0.13.3.tar.gz libmcrypt-2.5.7.tar.gz /usr/src/

RUN     cd /usr/src/ && tar -xzvf libmcrypt-2.5.7.tar.gz && cd libmcrypt-2.5.7 && ./configure && make && make install && /sbin/ldconfig \
	&& groupadd www && useradd -g www www \
        && cd /usr/src/ \
	&& tar -xzvf php-7.1.9.tar.gz \
	&& cd php-7.1.9 \
	&& ./configure --prefix=/usr/local/php --with-config-file-path=/usr/local/php/etc --with-config-file-scan-dir=/usr/local/php/conf.d \
	--enable-fpm --enable-sockets --with-fpm-user=www --with-fpm-group=www --enable-mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-libedit \
	--with-curl --with-openssl --with-zlib --enable-xml --enable-mbstring --enable-ftp --with-mhash --with-mcrypt --enable-bcmath --with-gmp --with-gd \
 	--enable-json --with-jpeg-dir --with-png-dir  --with-freetype-dir \
	&& make \
	&& make install \
	&& cd /usr/src/ \
	&& tar -xzvf v0.13.3.tar.gz \
	&& cd hiredis-0.13.3 \
	&& make -j \
	&& make install \
	&& ldconfig \
	&& source /etc/profile \
	&& cd /usr/src \
	&& tar -xzvf swoole-src-2.1.3.tar.gz \
	&& cd swoole-src-2.1.3 \
	&& phpize \
	&& ./configure --enable-async-redis  --enable-openssl \
	&& make clean \
	&& make -j \
	&& make install \
	&& pecl install redis \
	&& pecl install inotify \
	&& pecl clear-cache \
	&& yum clean all \
	&& mv /usr/src/composer.phar /usr/local/bin/composer \
	&& chmod 777 /usr/local/bin/composer \
	&& cd /usr/src \
	&& mkdir -p /usr/local/php/{etc,conf.d} \
	&& cp /usr/src/php-7.1.9/php.ini-production /usr/local/php/etc/php.ini \
	&& sed -i 's/post_max_size =.*/post_max_size = 50M/g' /usr/local/php/etc/php.ini \
  	&& sed -i 's/upload_max_filesize =.*/upload_max_filesize = 50M/g' /usr/local/php/etc/php.ini \
  	&& sed -i 's/;date.timezone =.*/date.timezone = PRC/g' /usr/local/php/etc/php.ini \
	&& sed -i 's/short_open_tag =.*/short_open_tag = On/g' /usr/local/php/etc/php.ini \
 	&& sed -i 's/;cgi.fix_pathinfo=.*/cgi.fix_pathinfo=0/g' /usr/local/php/etc/php.ini \
  	&& sed -i 's/max_execution_time =.*/max_execution_time = 300/g' /usr/local/php/etc/php.ini \
  	&& sed -i 's/disable_functions =.*/disable_functions = passthru,exec,system,chroot,scandir,chgrp,chown,shell_exec,proc_open,proc_get_status,popen,ini_alter,ini_restore,dl,openlog,syslog,readlink,symlink,popepassthru,stream_socket_server/g' /usr/local/php/etc/php.ini \
  	&& echo 'extension=swoole.so' >> /usr/local/php/etc/php.ini \
	&& echo 'extension=redis.so' >> /usr/local/php/etc/php.ini \
	&& echo 'extension=inotify.so' >> /usr/local/php/etc/php.ini

RUN cd /usr/src && rm -rf ./*

COPY php-fpm.conf /usr/local/php/etc/php-fpm.conf1

COPY php-fpm /usr/local/bin/php-fpm1

RUN chmod u+x /usr/local/bin/php-fpm1

WORKDIR /www

ENTRYPOINT ["php-fpm1"]

EXPOSE 9000 8081 8082 8083

CMD ["/bin/bash"]
