<?php

class apkParser {
    private $apkFile = '';
    private $unZipDir = '';
    private $AXMLPrinterPath = 'D:/SERVER/WWW/apkp/AXMLPrinter2.jar';
    private $AAPT = 'D:/SERVER/WWW/apkp/aapt.exe';
    private $scriptDir = 'D:/SERVER/WWW/apkp/';
    private $apkDetail = array();

    const SUCCESS = 0;
    const ERR_UNKNOWN = -100;
    const ERR_DIR_EXTIST = -101;
    const ERR_UNZIP_FAIL = -102;
    const ERR_MANIFEST_NOT_EXTIST = -103;
    const ERR_PARSE_MANIFEST = -104; //解悉失败
    const ERR_AAPT_CAN_NOT_USE = -105; //解悉失败

    public function __construct($apkFile, $unZipDir = "./tmp/") {
        if(file_exists($apkFile) && is_dir($unZipDir)) {
            $this->apkFile = $apkFile;
            $uniDir = md5($apkFile);
            $this->unZipDir = rtrim($unZipDir, '/') . '/' . $uniDir . '/';
        } else {
            return false;
        }
    }

    public function getApkDetail() {

        $state = $this->unZipApk();
        if(is_numeric($state) && $state < 0) {
            //解压失败
            return self::ERR_UNZIP_FAIL;
        } else {
            $this->getMoreDetail();
            // $this->getDetailFromManifest();
            return $this->apkDetail;
        }
    }

    //从AndroidManifest.xml中获取的apk信息
    public function getDetailFromManifest() {
        $manifestXML = $this->parseManifest();
        // var_dump($manifestXML);
        if(is_numeric($manifestXML) && $manifestXML < 0) {
            return $manifestXML;
        } else {
            $pat = '/android:versionCode="(\d+)"/';
            if(preg_match($pat, $manifestXML, $match) && count($match)>1) {
                $this->apkDetail["versionCode"] = $match[1];
            }
            $pat = '/android:versionName="([^"]+)"/';
            if(preg_match($pat, $manifestXML, $match) && count($match)>1) {
                $this->apkDetail["versionName"] = $match[1];
            }
            $pat = '/\bpackage="([^"]+)"/';
            if(preg_match($pat, $manifestXML, $match) && count($match)>1) {
                $this->apkDetail["package"] = $match[1];
            }
            $pat = '/android:icon="@([^"]+)"/';
            // $match[1] => 7F0200B8;
            // 342 => spec resource 0x7f0200b8 com.shafa.market:drawable/ic_launcher: flags=0x00000000
            // 1547 => resource 0x7f0200b8 com.shafa.market:drawable/ic_launcher: t=0x03 d=0x000000e7 (s=0x0008 r=0x00)
            //          (string8) "res/drawable-xhdpi/ic_launcher.png"
            if(preg_match($pat, $manifestXML, $match) && count($match)>1) {
                $this->apkDetail["pocode"] = $match[1];
            }

            $this->getIcon();
        }
    }

    private function getIcon() {
        $shellOrder = $this->AAPT.' dump --values resources ' . $this->apkFile;
        // $shellOrder = 'ping www.vipaq.com';
        $apkDumpData = shell_exec($shellOrder);
        // var_dump($apkDumpData);
        if($apkDumpData == NULL) {
            return self::ERR_AAPT_CAN_NOT_USE;
        } else {
            // echo $apkDumpData;echo '<br/>';
            $pat = '/\040\040\bresource 0x'.$this->apkDetail["pocode"].'[\s\S]+\(string8\) "([^"]+)"/iU';
            preg_match($pat, $apkDumpData, $match);
            if(preg_match($pat, $apkDumpData, $match) && count($match)>1) {
                $this->apkDetail["ori_icon_path"] = $match[1];
                $iconOriPath = $this->unZipDir . $this->apkDetail["ori_icon_path"];
                if(file_exists($iconOriPath)) {
                    $this->apkDetail["icon"] = md5($this->apkDetail["package"]).'.png';
                    copy($iconOriPath, './icon/'.$this->apkDetail["icon"]);
                }
            }
            
        }
    }

    public function parseManifest() {
        $manifestFile = $this->unZipDir . 'AndroidManifest.xml';
        $manifestFile = ltrim($manifestFile, '.');
        $manifestFile = ltrim($manifestFile, '/');
        $manifestFile = $this->scriptDir . $manifestFile;
        // echo $manifestFile;echo '<br/>';
        if(is_file($manifestFile)) {
            $shellOrder = 'java -jar '. $this->AXMLPrinterPath . ' ' . $manifestFile;
            $manifestXML = shell_exec($shellOrder);
            // echo $shellOrder;echo '<br/>';
            // var_dump($manifestXML);
            if($manifestXML) {
                return $manifestXML;
            } else {
                return self::ERR_PARSE_MANIFEST;
            }
        } else {
            return self::ERR_MANIFEST_NOT_EXTIST;
        }
    }

    private function unZipApk() {
        $state = self::ERR_UNKNOWN;
        if(is_dir($this->unZipDir)) {
            //解压目录已经存在
            // return self::ERR_DIR_EXTIST;

            //目录存在，不重新解压
            return self::SUCCESS;
        }
        $rs = @mkdir( $this->unZipDir, 0777 );
        if(file_exists($this->apkFile) && $rs) {
            $zip = new ZipArchive;
            if ($zip->open($this->apkFile) === TRUE) {
                $zip->extractTo($this->unZipDir);
                $zip->close();
                $state = self::SUCCESS;
            }
        }
        return $state;
    }

    public function getMoreDetail() {
        $shellOrder = $this->AAPT.' dump badging ' . $this->apkFile;
        $apkBadging = shell_exec($shellOrder);
        $apkBadgingArr = explode("\n",$apkBadging);
        if($apkBadging != "" && count($apkBadgingArr) > 1) {
            foreach($apkBadgingArr as $av) {
                if(strpos($av, "sdkVersion:'") === 0) {
                    $this->apkDetail["sdkVersion"] = rtrim(str_replace("sdkVersion:'", '', $av), "'");
                } else if(strpos($av, "uses-permission:'") === 0) {
                    $usesPermission = rtrim(str_replace("uses-permission:'", '', $av), "'");
                    $usesPermission = str_replace("android.permission.", '', $usesPermission);
                    if(! isset($this->apkDetail["usesPermission"])) {
                        $this->apkDetail["usesPermission"] = array();
                    }
                    $this->apkDetail["usesPermission"][] = $usesPermission;
                } else if(preg_match("/package: name='([^']+)'/", $av, $match) && count($match) > 1) {
                    $this->apkDetail["package"] = $match[1];
                    if(preg_match("/versionCode='(\d+)'/", $av, $match) && count($match) > 1) {
                        $this->apkDetail["versionCode"] = $match[1];
                    }
                    if(preg_match("/versionName='([^']+)'/", $av, $match) && count($match) > 1) {
                        $this->apkDetail["versionName"] = $match[1];
                    }
                } else if(preg_match("/application: label='([^']+)'/", $av, $match) && count($match) > 1) {
                    $this->apkDetail["appName"] = $match[1];
                    if(preg_match("/ icon='([^']+)'/", $av, $match) && count($match) > 1) {
                        $this->apkDetail["ori_icon_path"] = $match[1];
                        $iconOriPath = $this->unZipDir . $this->apkDetail["ori_icon_path"];
                        // echo $iconOriPath;
                        if(file_exists($iconOriPath)) {
                            $this->apkDetail["icon"] = md5($this->apkDetail["package"]).'.png';
                            copy($iconOriPath, './icon/'.$this->apkDetail["icon"]);
                        }
                    }
                } else if(strpos($av, "application-label:'") === 0) {

                }
            }
        }
        return self::SUCCESS;
    }
}

$apkFile = "D:/SERVER/WWW/apkp/EBHS.apk";
// $apkFile = "D:/soft/shafa.apk";
$apkp = new apkParser($apkFile);
$apkDetail = $apkp->getApkDetail();
echo '<img src="./icon/'.$apkDetail['icon'].'">';
echo '<br />';
echo '<pre>';
var_dump($apkDetail);
echo '</pre>';
