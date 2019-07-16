<?php

namespace App\UserListeners\Evaluation\CreateEvaluation;


use App\Models\Evaluation;
use App\UserEvents\Evaluation\CreateEvaluation;
use ZhiEq\Contracts\Listener;

class WriteEvaluation extends Listener
{

    /**
     * @param CreateEvaluation $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        return count($event->input['products']) === collect($event->input['products'])->map(function ($item) use ($event) {
                $orderProduct = $event->orderModel->orderProduct->firstWhere('product_code', $item['productCode']);
                $fields = [
                    'rate', 'content', 'images'
                ];
                return (new Evaluation())->fillable($fields)->fill(snake_case_array_keys(collect($item)->only(camel_case_array($fields))->toArray()))
                    ->setAttribute('user_code', auth_user()->code)
                    ->setAttribute('product_code', $item['productCode'])
                    ->setAttribute('specification_text', $orderProduct['specifications_text'])
                    ->setAttribute('order_code', $event->orderModel->code)
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
