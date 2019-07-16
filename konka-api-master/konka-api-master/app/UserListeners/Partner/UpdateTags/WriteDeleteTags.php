<?php

namespace App\UserListeners\Partner\UpdateTags;

use App\Models\PartnerTag;
use App\UserEvents\Partner\UpdateTags;
use ZhiEq\Contracts\Listener;

class WriteDeleteTags extends Listener
{

    /**
     * @param UpdateTags $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        return PartnerTag::wherePartnerCode($event->partnerModel->code)->whereIn('tag_code', $event->needDeleteTags->toArray())->delete() === $event->needDeleteTags->count();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 1;
    }
}
