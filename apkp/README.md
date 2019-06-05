#apkp

使用 php 配合 aapt 获取 apk 包内信息，容易扩展。

###【主要功能】：
获取apk软件详细信息，包括：
	1.应用名称
	2.应用icon
	3.软件包名
	4.软件所需权限
	5.适用的sdk版本号
	6.软件版本号


###【运行平台】：
	OS：Windows
	运行环境：java1.6以上

###【需要注意的地方】：
	需要将apkp.php中的参数替换成，脚本所在的目录。
	private $AXMLPrinterPath = '<base-path>/AXMLPrinter2.jar';
	private $AAPT = '<base-path>/aapt.exe';
	private $scriptDir = '<base-path>/';

###【关于我】：
	唐肇凯
	qq：327630285
	mail：tangzk@yeah.net
	blog：http://www.vipaq.com/