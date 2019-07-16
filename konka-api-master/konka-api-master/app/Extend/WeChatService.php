<?php

namespace App\Extend;

use Aws\S3\S3Client;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Http\StreamResponse;

class WeChatService
{
    /**
     * 生成小程序码
     *
     * @param string $scene
     * @param string $page
     * @return array
     */

    public static function generateCode($scene, $page)
    {
        return \Cache::tags(['shareCode'])->rememberForever(sha1(json_encode(['page' => $page, 'scene' => $scene])), function () use ($scene, $page) {
            /**
             * @var $response StreamResponse
             */
            $response = app('wechat.mini_program')->app_code->getUnlimit($scene, [
                'page' => 'pages/product-detail/main'
            ]);
            $response->getBody()->rewind();
            $contents = $response->getBody()->getContents();
            if (empty($contents) || '{' === $contents[0]) {
                throw new RuntimeException('Invalid media response content.');
            }
            /**
             * @var S3Client $s3
             */
            $s3 = app('minio');
            $fileName = sha1(microtime(true));
            $s3->putObject([
                'Bucket' => config('filesystems.disks.minio.bucket'),
                'Key' => $fileName,
                'Body' => $contents
            ]);
            return ['fileName' => $fileName, 'fileUrl' => upload_file_to_url($fileName)];
        });
    }
}
