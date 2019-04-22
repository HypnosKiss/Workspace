#!/bin/bash
while [ 1 ]
do
        wget -T5 -t1 http://127.0.0.1/apache_check.php > /dev/null 2>&1
        if [ $? -ne 0 ]
        then
                apachectl restart >/dev/null 2>&1
                killall php-fpm 2>&1
                /alidata/soft/php-5.6.9/sbin/php-fpm 2>&1
                echo "apache restart at `date +%y-%m-%d\ %H:%M:%S`" >> /alidata/auto_apache_restart.log
        else
                wt=`cat apache_check.php`
                if [ $wt != 'OK' ]
                then
                        apachectl restart > /dev/null 2>&1
                        killall php-fpm 2>&1
                        /alidata/soft/php-5.6.9/sbin/php-fpm 2>&1
                        echo "apache restart at `date +%y-%m-%d\ %H:%M:%S`" >> /alidata/auto_apache_restart.log
                fi
        fi
rm -f apache_check.php
rm -f apache_check.php.1
sleep 2
done
