#!/bin/bash

#备份数据库
/alidata/soft/mysql-5.7.18/bin/mysqldump -uqbteigze -pWeb2012! -A --skip-add-locks --force |gzip > /alidata/sqlbackup/sql_$(date +"%Y%m%d%H%M%S").sql.gz

#删除7天前程序运行日志
find /alidata/www -mtime +3 -name "*[0-9][0-9]_[0-9][0-9]_[0-9][0-9].log"  -exec rm -f {} \;

#删除7天前数据库备份
find /alidata/sqlbackup -mtime +7 -name "*.sql.gz"  -exec rm -f {} \;
