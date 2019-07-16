<?php

namespace App\UserEvents\Partner;


use App\Models\Partner;
use App\Models\PartnerTag;
use ZhiEq\Exceptions\CustomException;

class UpdateTags
{
    /**
     * @var \Illuminate\Support\Collection
     */

    public $needCreateTags;

    /**
     * @var \Illuminate\Support\Collection
     */

    public $needDeleteTags;

    /**
     * @var Partner|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */

    public $partnerModel;

    /**
     * UpdateTags constructor.
     * @param $code
     * @param $input
     */

    public function __construct($code, $input)
    {
        if (!$this->partnerModel = Partner::whereCode($code)->first()) {
            throw new CustomException('产品不存在');
        }
        $needTags = collect($input);
        $hasTags = PartnerTag::wherePartnerCode($code)->get()->pluck('tag_code');
        $this->needCreateTags = $needTags->diff($hasTags);
        $this->needDeleteTags = $hasTags->diff($needTags);
    }
}
