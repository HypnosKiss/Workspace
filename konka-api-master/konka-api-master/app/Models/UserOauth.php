<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;

/**
 * App\Models\UserAuths
 *
 * @property int $id
 * @property string $user_code 用户编码
 * @property int $type 类型
 * @property string $open_id openId
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserOauth newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserOauth newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserOauth query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserOauth whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserOauth whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserOauth whereOpenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserOauth whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserOauth whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserOauth whereUserCode($value)
 * @mixin \Eloquent
 * @method static array getTypeLabels()
 * @method static string getTypeLabel($key)
 * @method static array getTypeList()
 * @property-read \App\Models\User $user
 */
class UserOauth extends Model
{
    use DefinitionAttribute;

    const TYPE_WX_APPLETS = 10;
    const TYPE_WX_UNION_ID = 20;

    protected $hidden = [
        'user'
    ];

    /**
     * 关联用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function user()
    {
        return $this->hasOne('App\Models\User', 'code', 'user_code');
    }

    /**
     * @return array
     */

    protected static function typeDefinition()
    {
        return [
            self::TYPE_WX_APPLETS => '微信小程序',
            self::TYPE_WX_UNION_ID => '微信开放平台',
        ];
    }
}
