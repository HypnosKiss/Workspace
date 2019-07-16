<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SearchRecord
 *
 * @property int $id
 * @property string $content 内容
 * @property int $num 次数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchRecord whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchRecord whereNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchRecord whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $user_code 用户编码
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SearchRecord whereUserCode($value)
 */
class SearchRecord extends Model
{

}