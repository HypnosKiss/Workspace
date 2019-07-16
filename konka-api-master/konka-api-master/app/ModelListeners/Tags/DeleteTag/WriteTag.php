<?php

namespace App\ModelListeners\Tags\DeleteTag;


use App\ModelEvents\Tags\DeleteTag;
use ZhiEq\Contracts\Listener;

class WriteTag extends Listener
{

    /**
     * @param DeleteTag $event
     * @return boolean|string|array
     * @throws \Exception
     */
    public function handle($event)
    {
        return $event->tagModel->delete();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}
