FROM ilovintit/php71-apache-extend
MAINTAINER ilovinti <ilovintit@gmail.com>

RUN docker-php-ext-install pcntl bcmath
RUN docker-php-ext-enable opcache

#部署代码
RUN mkdir -p /app
WORKDIR /app
COPY ./composer.json /app/
COPY ./composer.lock /app/
RUN composer install --prefer-dist  --no-autoloader --no-scripts
COPY . /app
RUN composer install --prefer-dist
RUN chown -R www-data:www-data /app

#配置虚拟主机
RUN rm -rf /var/www/html && ln -s /app/public /var/www/html

#配置supervisor

COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord"]
