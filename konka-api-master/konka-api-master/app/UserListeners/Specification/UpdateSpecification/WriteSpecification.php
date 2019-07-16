<?php

namespace App\UserListeners\Specification\UpdateSpecification;


use App\Models\Specification;
use App\UserEvents\Specification\UpdateSpecification;
use ZhiEq\Contracts\Listener;
use ZhiEq\Exceptions\CustomException;

class WriteSpecification extends Listener
{

    /**
     * @param UpdateSpecification $event
     * @return boolean|string|array
     */
    public function handle($event)
    {
        $fields = [
            'parent_code', 'name', 'order', 'remark'
        ];
        $model = $event->specificationModel->fillable($fields)->fill(snake_case_array_keys(collect($event->input)->only(camel_case_array($fields))->toArray()));
        if (!empty($event->input['parentCode'])) {
            $specification = Specification::whereCode($event->input['parentCode'])->first();
            if ($specification->level !== 1) {
                throw new CustomException('只支持二级分类');
            }
            $model->setAttribute('level', $specification->level + 1);
        }
        return $model->save();
    }

    /**
     * @return integer
     */
    public function order()
    {
        return 0;
    }
}