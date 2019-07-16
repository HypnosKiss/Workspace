<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

if (!function_exists('upload_file_to_url')) {

    /**
     * @param $filename
     * @return null|string
     */

    function upload_file_to_url($filename)
    {
        return config('filesystems.disks.oss.cdn').$filename;
        /*if (empty($filename)) {
            return null;
        }*/
        /**
         * @var Aws\S3\S3Client $s3
         */
        /*$s3 = app('minio');
        $command = $s3->getCommand('GetObject', [
            'Bucket' => config('filesystems.disks.minio.bucket'),
            'Key' => $filename
        ]);
        $presignedRequest = $s3->createPresignedRequest($command, '+10 minutes');
        return (string)$presignedRequest->getUri();*/
    }

}

if (!function_exists('get_upload_url')) {

    function get_upload_url()
    {
        /**
         * @var Aws\S3\S3Client $s3
         */
        $s3 = app('minio');
        $fileName = sha1(microtime(true));
        $command = $s3->getCommand('PutObject', [
            'Bucket' => config('filesystems.disks.minio.bucket'),
            'Key' => $fileName,

        ]);
        $presignedRequest = $s3->createPresignedRequest($command, '+15 minutes');
        $presignedUrl = (string)$presignedRequest->getUri();
        return success(['url' => $presignedUrl, 'filename' => $fileName]);
    }

}

if (!function_exists('auth_admin')) {

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null|\App\Models\Admin
     */

    function auth_admin()
    {
        return Auth::guard('admin')->user();
    }
}

if (!function_exists('auth_user')) {

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null|\App\Models\User
     */

    function auth_user()
    {
        return Auth::guard('api')->user();
    }
}

if (!function_exists('export_excel')) {

    /**
     * @param string $name
     * @param array $data
     * @param array $head
     * @param array $keys
     * @return mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */

    function export_excel($name = '测试表', $data = [], $head = [], $keys = [])
    {
        $count = count($head);

        $patch = storage_path() . '\\' . $name . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        for ($i = 0; $i < $count; $i++) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . '1', $head[$i]);
        }

        foreach ($data as $key => $item) {
            for ($i = 0; $i < $count; $i++) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($i + 1) . ($key + 2), $item[$keys[$i]]);
            }
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($patch);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        $result = app('minio')->putObject([
            'Bucket' => config('filesystems.disks.minio.bucket'),
            'Key' => $name . '.xlsx',
            'Body' => fopen($patch, 'r')
        ]);

        unset($writer);
        unset($result);
        gc_collect_cycles();
        unlink($patch);

        return upload_file_to_url($name . '.xlsx');
    }
}
