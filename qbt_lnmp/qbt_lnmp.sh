#!/bin/bash
########################
# 一键快速搭建服务器环境
########################
read -n 1 -p "Is there a data disk?(y/n):" fm
echo -e "\n"

data_dev="/dev/vdb"
data_dir="/data"
www_dir="${data_dir}/www"
soft_dir="${data_dir}/soft"
lnmp_dir="${soft_dir}/lnmp"

if [ "$fm" == "y" ];then
fdisk -S 56 $data_dev << EOF
n
p
1


wq
EOF

sleep 5
mkfs.ext4 ${data_dev}1
fi

mkdir $data_dir
mount ${data_dev}1 $data_dir
mkdir $www_dir
mkdir $soft_dir
mv lnmp $soft_dir
cd $lnmp_dir

yum -y install screen

screen ./install.sh