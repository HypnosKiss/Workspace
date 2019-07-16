<?php

namespace App\UserEvents\Evaluation;


use App\Models\Evaluation;
use Validator;
use ZhiEq\Exceptions\CustomException;

class CreateEvaluationReply
{
    /**
     * @var array $input
     */

    public $input;

    /**
     * @var Evaluation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */

    public $evaluationModel;

    /**
     * CreateEvaluationReply constructor.
     * @param $input
     * @param $code
     * @throws \Illuminate\Validation\ValidationException
     */

    public function __construct($input, $code)
    {
        if (!$this->evaluationModel = Evaluation::whereCode($code)->first()) {
            throw new CustomException('编码不存在');
        }
        Validator::validate($input, $this->rules(), $this->message());
        $this->input = $input;
    }

    protected function rules()
    {
        return [
            'content' => ['required']
        ];
    }

    protected function message()
    {
        return [
            'content.required' => '评价内容不能为空'
        ];
    }
}