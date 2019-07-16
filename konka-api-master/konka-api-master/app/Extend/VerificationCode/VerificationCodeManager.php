<?php

namespace App\Extend\VerificationCode;

use Carbon\Carbon;
use Faker\Provider\Uuid;

class VerificationCodeManager
{
    private $cachePrefix = 'verification_code';

    protected $codeId;
    protected $recipient;
    protected $code;

    /**
     * @var \Illuminate\Cache\Repository|\Laravel\Lumen\Application|mixed
     */
    private $cache;

    /**
     * VerificationCodeManager constructor.
     */

    function __construct()
    {
        $this->cache = app('cache');
    }

    public function generateCode()
    {
        return rand(100000, 999999);
    }

    public function setCodeId($codeId)
    {
        app('log')->info('Verification', ['codeId' => $codeId, 'thisCodeId' => $this->codeId]);
        if ($codeId !== $this->codeId) {
            $this->codeId = $codeId;
            list($this->recipient, $this->code) = $this->cache->tags([$this->cachePrefix])->get($codeId, [null, null]);
        }
        app('log')->info('VerificationData:', ['codeId' => $this->codeId, 'recipient' => $this->recipient, 'code' => $this->code]);
        return $this;
    }

    public function getRecipient()
    {
        return $this->recipient;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function saveCode($recipient, $code, $expiredTime = null)
    {
        $expiredTime = $expiredTime === null ? config('tools.verification_code_expired_time') : $expiredTime;
        $codeId = Uuid::uuid();
        app('log')->info('saveCodeData:', ['recipient' => $recipient, 'code' => $code]);
        $this->cache->tags($this->cachePrefix)->put($codeId, [$recipient, $code], Carbon::now()->addSeconds($expiredTime));
        return $codeId;
    }

    public function generateAndSave($recipient, $expiredTime = null)
    {
        $code = $this->generateCode();
        $smsId = $this->saveCode($recipient, $code, $expiredTime);
        return [$smsId, $code];
    }

    public function validateCodeId($codeId, $recipient)
    {
        return $this->setCodeId($codeId)->getRecipient() === $recipient;
    }

    public function validateSaveCode($codeId, $code)
    {
        return $this->setCodeId($codeId)->getCode() === (int)$code;
    }

    public function destroySaveCode($codeId)
    {
        $this->cache->tags([$this->cachePrefix])->forget($codeId);
    }

    public function validateAndDestroySaveCode($codeId, $code, $force = false)
    {
        $validateResult = $this->validateSaveCode($codeId, $code);
        if ($validateResult === true || $force === true) {
            $this->destroySaveCode($codeId);
        }
        return $validateResult;
    }
}
