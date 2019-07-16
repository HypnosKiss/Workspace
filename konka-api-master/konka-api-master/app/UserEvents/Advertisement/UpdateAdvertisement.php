<?php

namespace App\UserEvents\Advertisement;


use App\Models\Advertisement;
use Illuminate\Validation\Rule;
use Validator;
use ZhiEq\Exceptions\CustomException;

class UpdateAdvertisement
{
    /**
     * @var array $input
     */

    public $input;

    /**
     * @var Advertisement|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */

    public $advertisementModel;

    /**
     * UpdateAdvertisement constructor.
     * @param $input
     * @param $code
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input, $code)
    {
        if (!$this->advertisementModel = Advertisement::whereCode($code)->first()) {
            throw new CustomException('编码无效');
        }
        Validator::validate($input, $this->rules(), $this->message());
        $this->input = $input;
    }

    protected function rules()
    {
        return [
            'title' => ['required'],
            'content' => ['required'],
            'position' => ['required', Rule::in(Advertisement::getPositionList())]
        ];
    }

    protected function message()
    {
        return [
            'content.required' => '广告图片不能为空',
            'title.required' => '广告名称不能为空',
            'position.required' => '必须选择广告位置',
            'position.in' => '广告位置不正确'
        ];
    }
}
