<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;


/**
 * App\Models\CustomServiceClient
 *
 * @property int $id
 * @property string $code 会话编码
 * @property string $user_code 用户编码
 * @property int $message_type 消息类型
 * @property string $last_message 最后一条消息内容
 * @property int $status 会话状态
 * @property \Illuminate\Support\Carbon $last_send_at 最后消息发送时间
 * @property int $unread_num 未读消息数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $last_send_time
 * @property-read mixed $nickname
 * @property-read string|null $username
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomServiceClient newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomServiceClient newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomServiceClient query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomServiceClient whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomServiceClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomServiceClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomServiceClient whereLastMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomServiceClient whereLastSendAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomServiceClient whereMessageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomServiceClient whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomServiceClient whereUnreadNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomServiceClient whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomServiceClient whereUserCode($value)
 * @mixin \Eloquent
 */
class CustomServiceClient extends Model
{
    use DefinitionAttribute;

    const STATUS_UNREAD = 10;
    const STATUS_READ = 20;

    protected static $codePrefix = 'CSC';

    protected $appends = [
        'username', 'nickname', 'last_send_time'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_send_at'
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function (self $model) {
            $model->code = CodeGenerator::getUniqueCode(self::class, function () {
                return self::maxCode();
            }, 10, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::$codePrefix);
        });
    }

    /**
     * @return bool|int|string
     */

    protected static function maxCode()
    {
        if ($model = self::orderByDesc('code')->first()) {
            return substr($model->code, strlen(self::$codePrefix));
        }
        return 0;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_code', 'code');
    }

    /**
     * @return mixed
     */

    public function clearUnread()
    {
        return $this->setAttribute('unread_num', 0)->setAttribute('status', self::STATUS_READ)->save();
    }

    /**
     * @return string|null
     */

    public function getUsernameAttribute()
    {
        return $this->user->username;
    }

    /**
     * @return mixed
     */

    public function getNicknameAttribute()
    {
        return $this->user->nickname;
    }

    /**
     * @return string
     */

    public function getLastSendTimeAttribute()
    {
        return $this->last_send_at->isCurrentDay() ? $this->last_send_at->format('H:i:s') : $this->last_send_at->format('Y/m/d');
    }
}
