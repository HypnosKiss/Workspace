<?php

namespace App\UserListeners\Partner\UpdateTags;

use App\Models\PartnerTag;
use App\UserEvents\Partner\UpdateTags;
use ZhiEq\Contracts\Listener;

class WriteCreateTags extends Listener
{

    /**
     * @param UpdateTags $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return $event->needCreateTags->count() === $event->needCreateTags->filter(function ($tag) use ($event) {
                return (new PartnerTag())
                    ->setAttribute('partner_code', $event->partnerModel->code)
                    ->setAttribute('tag_code', $tag)
                    ->save();
            })->count();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
