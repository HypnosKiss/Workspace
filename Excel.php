<?php
/**
 * @author    【develop41】【wili.lixiang@gmail.com】 【2019/9/16 10:55:39】
 * @package   QShop [ WE CAN DO IT JUST THINK IT ]
 * @Copyright (c) 2013 http://www.qbt8.com All rights reserved.
 * @uses
 * @license   千百特科技有限公司
 */

class Excel
{
    // 1
    public function exportToExcel($fileName = '', array $header = [], $data)
    {

        ini_set('max_execution_time', 300);// 设置PHP超时时间
        ini_set('memory_limit', '2048M');// 设置PHP临时允许内存大小
        $queryResult = $data;

        //路径
        $fileName .= date('Ymd_His') . '.csv';
        $filePath = 'excel/' . $fileName;
        $index    = 0;
        $fp       = fopen($filePath, 'w'); //生成临时文件
        chmod($filePath, 0777);//修改可执行权限
        // 将数据通过fputcsv写到文件句柄
        fputcsv($fp, $header);
        //处理导出数据
        foreach ($queryResult as $key => &$val) {
            foreach ($val as $k => $v) {
                $val[$k] = $v . "\t";
                if ($index == 10000) { //每次写入1000条数据清除内存
                    $index = 0;
                    ob_flush();//清除内存
                    flush();
                }
                $index++;
            }
            fputcsv($fp, $val);
        }
        ob_flush();
        fclose($fp);  //关闭句柄
        header("Cache-Control: max-age=0");
        header("Content-type:application/vnd.ms-excel;charset=UTF-8");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename=' . basename($fileName));
        header("Content-Type: text/csv");
        header("Content-Transfer-Encoding: binary");
        header('Content-Length: ' . filesize($filePath));
        @readfile($filePath);//输出文件;
        unlink($filePath); //删除压缩包临时文件
        echo $filePath;

        return $filePath;
    }



    //获取客户端操作系统类型
    public function get_user_os(){
        if(!empty($_SERVER['HTTP_USER_AGENT'])){
            $user_os=$_SERVER['HTTP_USER_AGENT'];
            if(preg_match('/win/i',$user_os)){
                $user_os='windows';
            }else if(preg_match('/linux/i',$user_os)){
                $user_os='linux';
            }else if(preg_match('/unix/i',$user_os)){
                $user_os='unix';
            }else if(preg_match('/mac/i',$user_os)){
                $user_os='Macintosh';
            }else{
                $user_os='other';
            }
            return $user_os;
        }
    }

    // 2
    public function exportSpreadsheetCsv($arrData, $fileName)
    {
        $delimiter = ',';
        if ($this->get_user_os() === 'Macintosh') {
            $delimiter = ';';
        }

        header('Content-Description: File Transfer');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $fileName . '.csv');
        header('Cache-Control: max-age=0');
        $fp = fopen('php://output', 'a'); //打开output流
        //mb_convert_variables('GBK', 'UTF-8', $arrData['title']);
        fputcsv($fp, $arrData['title'], $delimiter); //将数据格式化为csv格式并写入到output流中

        $dataNum = count($arrData['content']);
        $perSize = 1000; //每次导出的条数
        $pages   = ceil($dataNum / $perSize);
        for ($i = 1; $i <= $pages; $i++) {
            $step = ($i * $perSize) - 1;
            foreach ($arrData['content'] as $key => $item) {
                if ($key > $step || $key <= $step - $perSize) {
                    continue;
                }
                // mb_convert_variables('GBK', 'UTF-8', $item);
                fputcsv($fp, $item, $delimiter);
            }
            //刷新输出缓冲到浏览器
            flush(); //必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
        }
        fclose($fp);
        unset($arrData);
        exit;
    }

    //3
    public function exportSpreadsheetCsv1($arrData, $fileName)
    {
        // csv文件内容不要以字母开始
        $title = '报表'."\n";
        // 准备字段
        $titles = [
            'id' => 'ID',
            'type' => '类型',
            'content' => '内容',
            'create_time' => '创建时间',
            'mark' => '备注'
        ];
        $fields = '';
        foreach ($titles as $k => $v) {
            $title .= $v.',';
            $fields .= $k.',';
        }
        $fields = rtrim($fields, ',');
        // 数据库查询
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=test', 'root', 'root');
        $res = $pdo->query('SELECT '.$fields.' from excel_test LIMIT 100000');
        $res = $res->fetchAll(PDO::FETCH_ASSOC);
        //  结果处理
        $csv = $title."\n";
        $fields = explode(',', $fields);
        foreach ($res as $value) {
            $row = '';
            foreach ($fields as $field) {
                // 按照 fputcsv() 函数的处理方式
                if (strpos($value[$field],',') !== FALSE || strpos($value[$field],'"') !== FALSE) {
                    $row .= '"'.str_replace('"','""',$value[$field]).'",';
                }else{
                    $row .= $value[$field].',';
                }
            }
            $csv .= $row."\n";
        }
        file_put_contents('./test.csv',mb_convert_encoding($csv, "GBK", "UTF-8"),FILE_APPEND);
    }

    // 4
    public function exportSpreadsheetCsv2($arrData, $fileName)
    {
        $startMemory = memory_get_usage();
        $sql = 'select * from user';
        $pdo = new \PDO('mysql:host=127.0.0.1;dbname=test', 'root', 'root');
        $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $rows = $pdo->query($sql);
        $filename = date('Ymd') . '.csv'; //设置文件名
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment;filename={$filename}");
        $out = fopen('php://output', 'w');
        fputcsv($out, ['id', 'username', 'password', 'create_time']);

        foreach ($rows as $row) {
            $line = [$row['id'], $row['username'], $row['password'], $row['create_time']];

            fputcsv($out, $line);
        }

        fclose($out);
        $memory = round((memory_get_usage() - $startMemory) / 1024 / 1024, 3) . 'M' . PHP_EOL;
        file_put_contents('/tmp/test.txt', $memory, FILE_APPEND);
    }

    // 5
    public function exportSpreadsheetCsv3($arrData, $fileName)
    {
        header ( "Content-type:application/vnd.ms-excel" );
        header ( "Content-Disposition:filename=" . iconv ( "UTF-8", "GB18030", "query_user_info" ) . ".csv" );

        // 打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');

        // 将中文标题转换编码，否则乱码
        foreach ($column_name as $i => $v) {
            $column_name[$i] = iconv('utf-8', 'GB18030', $v);
        }
        // 将标题名称通过fputcsv写到文件句柄
        fputcsv($fp, $column_name);

        $pre_count = 10000;
        for ($i=0;$i<intval($total_export_count/$pre_count)+1;$i++){
            $export_data = $db->getAll($sql." limit ".strval($i*$pre_count).",{$pre_count}");
            foreach ( $export_data as $item ) {
                $rows = array();
                foreach ( $item as $export_obj){
                    $rows[] = iconv('utf-8', 'GB18030', $export_obj);
                }
                fputcsv($fp, $rows);
            }

            // 将已经写到csv中的数据存储变量销毁，释放内存占用
            unset($export_data);
            ob_flush();
            flush();
        }

        exit ();
    }


}