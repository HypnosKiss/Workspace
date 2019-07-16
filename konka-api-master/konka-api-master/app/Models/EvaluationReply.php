<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EvaluationReply
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EvaluationReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EvaluationReply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EvaluationReply query()
 * @mixin \Eloquent
 */
class EvaluationReply extends Model
{
    const TYPE_USER = 10;  //用户
    const TYPE_ADMIN = 20;   //管理员
}