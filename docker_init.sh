#!/bin/bash
docker start api win java
#ra3 oracle_server2
docker exec win apachectl start
docker exec api service nginx start
docker exec api /usr/sbin/php-fpm7.0 -R
#docker exec ra3 apachectl start
#docker exec oracle_server2 service oracle-xe start
