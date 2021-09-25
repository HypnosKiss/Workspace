#!/bin/sh

IFS_old=$IFS
IFS=$'\n'

# log 文件
log_dir='/tmp/sh'
log_file="$log_dir/screen_job_sh.log"
# 脚本配置文件
config_file="/wwwdata/sh/screen_job.conf"
# 执行目录
exec_dir='~'

mkdir -p $log_dir
touch $log_file

# return the current date time
timestamp(){
    echo $(date "+%Y-%m-%d %H:%M:%S")
}

# 颜色字
msg(){
	local msg=$1
	echo -e "\033[1;36m $msg \033[0m"
}

# 保存日志
outputLog(){
  msg "$(timestamp) $1"
  echo "$(timestamp) $1" >> $log_file;
}

# 发送一个命令给screen
sendCmd(){
	local screenName=$1
	local cmd=$2
	screen -x -S $screenName -p 0 -X stuff "$cmd"
	screen -x -S $screenName -p 0 -X stuff $'\n'
}

# 在一个screen内创建任务
addJob(){
	local screenName=$1
	local cmd

	cmd=$"cd $exec_dir";
	sendCmd $screenName "$cmd" #用引号防止传参被拆分成多个参数

	outputLog "$screenName Start performing task $cmd"

	cmd=$2;
	sendCmd $screenName "$cmd"

	outputLog "$screenName $cmd The task has been performed"
}

# 停止 screen 内创建的任务 SIGINT screen -X -S $screenName -p 0 -X stuff $'\003' 或者 screen -X -S $screenName -p 0 -X stuff "^C"
stopJob(){
	local screenName=$1
  outputLog "$screenName Start stopping the task $cmd"
	screen -X -S $screenName -p 0 -X stuff "^C"
	outputLog "$screenName $cmd The task has stopped"
}

# 停止一个screen
stopScreen(){
	local screenName=$1
	local exit=$2

	stopJob $screenName

	if [ $? -ne 0 ];then
		return
	fi

	#这里需要业务界定是否需要退出当前 screen 默认退出，false 不退出当前 screen
	if [[ $exit != 'false' ]]; then #	if [ ${exit}x != 'falsex' ]; then
    outputLog "stop screen $screen_name"
	  local cmd=$"exit";
	  sendCmd $screenName "$cmd"
	fi
}

#启动一个screen
startScreen(){
	local screenName=$1
	outputLog "begin screen $screen_name"
	if screen -ls|grep "\<$screenName\>" &>/dev/null;then
		outputLog "screen $screenName already running... "
		return
	fi
	screen -dmS $screenName
}

# 用法
usage(){
    echo $"Usage: $0 {help|status|start|stop|restart|stopJob|restartJob}"
    exit 1
}

# 查看操作说明
menu() {
cat <<-EOF
###############################################################################
#       help: See the instructions                                            #
#       status: View screen Tasks                                             #
#       start: launch screen And perform the task                             #
#       stop: Interrupt task first, then exit screen                          #
#       restart: To perform first stop Operation to perform start Operation   #
#       stopJob: Interrupt tasks                                              #
#       restartJob: Interrupt tasks, and then start the task                  #
###############################################################################
EOF
}

batchHandle(){
  for line in $(cat $config_file)
  do
    command=${line##*-}
    screen_name=${line%-*}
    command=$(echo $command | sed 's/\r//')

    outputLog "action: $action screen name: $screen_name command: $command"

    pid=$(ps -ef | grep $command | grep -v grep | awk '{print $2}')

    local quit='';
    if [ $action = "stopJob" -o $action = "restartJob" ]; then
       quit='false';
    fi

    if [ ! $pid ]; then

      #根据控制指令决定做什么
      case "$action" in
        start)
          outputLog "The task is about to begin"
          startScreen $screen_name
          addJob $screen_name $command
          outputLog "The task has already been begun"
          ;;
        stop | stopJob)
          outputLog "The task does not exist"
          ;;
        restart | restartJob)
          outputLog "The task does not exist and is about to begin"
          startScreen $screen_name
          addJob $screen_name $command
          outputLog "The task does not exist and has already been begun"
          ;;
        *)
          usage
          ;;
      esac

    else
      #根据控制指令决定做什么
      case "$action" in
        start)
          outputLog "The task already exists PID：$pid ";
          ;;
        stop | stopJob)
          outputLog "The task is about to stop";
          stopScreen $screen_name $quit
          outputLog "The task has already been stopped";
          ;;
        restart | restartJob)
          outputLog "The task already exists PID：$pid and is about to restart";
          stopScreen $screen_name $quit
          startScreen $screen_name
          addJob $screen_name $command
          outputLog "The task already exists PID：$pid and has already been restarted";
          ;;
        *)
          usage
          ;;
      esac
    fi
  done
}

###################### main ########################
main() {
  # 根据控制指令决定做什么 {help|status|start|stop|restart|stopJob|restartJob}
  case "$action" in
    start | stop | restart | stopJob | restartJob)
      batchHandle $action
      ;;
    status)
      screen -ls
      exit 0
      ;;
    help)
      menu
      ;;
    *)
      usage
      ;;
  esac

  echo
  # 如果是启停控制，也打印一下status
  if [ $action = "start" -o $action = "stop"  -o $action = "restart" ];then
    msg "$action screen result:"
    screen -ls
  fi
}

# 位置参数
action=$1

# 只允许一个位置参数
[ $# -ne 1 ] && usage

main

IFS=$IFS_old