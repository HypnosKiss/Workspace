<?php

namespace App\Http\Controllers;

use App\Extend\KonKaWebservice;
use App\Models\PublicDefinition;
use App\Models\Setting;
use App\UserEvents\Settings\SaveSetting;
use App\UserEvents\VerificationCode\SendSmsCode;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use ZhiEq\Contracts\Controller;
use ZhiEq\Utils\Trigger;

class BaseInfoController extends Controller
{
    /**
     * 读取设置
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getSetting()
    {
        return success(collect(Setting::getSettingKeyList())->mapWithKeys(function ($key) {
            return Setting::isImage($key) ? [
                $key => Setting::getValue($key),
                $key . 'Url' => !empty(Setting::getValue($key)) ? upload_file_to_url(Setting::getValue($key)) : ''
            ]
                : [
                    $key => Setting::getValue($key)
                ];
        })->toArray());
    }


    /**
     * 保存设置
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function saveSetting(Request $request)
    {
        if (!Trigger::eventWithTransaction(new SaveSetting($request->only(Setting::getSettingKeyList())))) {
            return errors('保存失败');
        }
        return success([], '保存成功');
    }

    /**
     * 图片上传
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage()
    {
        /**
         * @var S3Client $s3
         */
        $s3 = app('minio');
        $fileName = sha1(microtime(true));
        $s3->putObject([
            'Bucket' => config('filesystems.disks.minio.bucket'),
            'Key' => $fileName,
            'Body' => fopen($_FILES["file"]["tmp_name"], 'r')
        ]);
        return success(['filename' => $fileName, 'url' => upload_file_to_url($fileName)]);
    }

    /**
     * 上传图片地址
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getUploadUrl()
    {
        return get_upload_url();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getStatusList()
    {
        return success(definition_to_select(PublicDefinition::getStatusLabels()));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function getPermissionList()
    {
        return success(definition_to_select(PublicDefinition::getPermissionLabels()));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */

    public function sendSmsCode(Request $request)
    {
        $event = new SendSmsCode($request->input());
        if (!$result = Trigger::eventResult($event)) {
            return errors('发送短信失败');
        }
        return success($result, '发送短信成功', ['X-Code' => $event->code]);
    }
}
