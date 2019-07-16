<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;

/**
 * App\Models\CustomerServiceMessage
 *
 * @property int $id
 * @property int $type 类型
 * @property int|null $message_type 内容类型
 * @property string $content 内容
 * @property string $user_code 创建人编码
 * @property string|null $admin_code 回复人编码
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Admin $admin
 * @property-read null|string $avatar
 * @property-read null|string $content_url
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceMessage whereAdminCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceMessage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceMessage whereMessageType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceMessage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CustomerServiceMessage whereUserCode($value)
 * @mixin \Eloquent
 * @method static getTypeList()
 * @method static getTypeLabels()
 * @method static getTypeLabel($key)
 * @method static getMessageTypeList()
 * @method static getMessageTypeLabels()
 * @method static getMessageTypeLabel($key)
 * @property-read mixed $message_type_text
 * @property-read string|null $send_name
 * @property-read string|null $send_username
 * @property-read mixed $type_text
 * @property-read mixed $admin_nickname
 * @property-read mixed $admin_username
 * @property-read mixed $user_nickname
 * @property-read mixed $user_username
 */
class CustomerServiceMessage extends Model
{
    use DefinitionAttribute;

    const TYPE_USER = 10;
    const TYPE_ADMIN = 20;

    const MESSAGE_TYPE_STRING = 10;
    const MESSAGE_TYPE_IMAGE = 20;

    protected $appends = [
        'avatar', 'content_url',
        'type_text', 'message_type_text',
        'send_name', 'send_username'
    ];

    protected static function typeDefinition()
    {
        return [
            self::TYPE_USER => '会员',
            self::TYPE_ADMIN => '客服'
        ];
    }

    protected static function messageTypeDefinition()
    {
        return [
            self::MESSAGE_TYPE_STRING => '文本',
            self::MESSAGE_TYPE_IMAGE => '图片'
        ];
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'code', 'user_code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function admin()
    {
        return $this->hasOne('App\Models\Admin', 'code', 'admin_code');
    }

    /**
     * @return null|string
     */

    public function getAvatarAttribute()
    {
        if (empty($this->user)) {
            return null;
        }
        return $this->user->avatar;
    }

    /**
     * @return null|string
     */

    public function getContentUrlAttribute()
    {
        if ($this->message_type === self::MESSAGE_TYPE_STRING) {
            return null;
        }
        return upload_file_to_url($this->content);
    }

    /**
     * @return mixed
     */

    public function getTypeTextAttribute()
    {
        return self::getTypeLabel($this->type);
    }

    /**
     * @return mixed
     */

    public function getMessageTypeTextAttribute()
    {
        return self::getMessageTypeLabel($this->message_type);
    }

    /**
     * @return string
     */

    public function getAdminNicknameAttribute()
    {
        if (empty($this->admin)) {
            return '[管理员已删除]';
        }
        return $this->admin->nickname;
    }

    /**
     * @return string|null
     */

    public function getUserNicknameAttribute()
    {
        return $this->user->nickname;
    }

    /**
     * @return string
     */

    public function getAdminUsernameAttribute()
    {
        if (empty($this->admin)) {
            return '[账号已删除]';
        }
        return $this->admin->username;
    }

    /**
     * @return string|null
     */

    public function getUserUsernameAttribute()
    {
        return $this->user->username;
    }

    /**
     * @return string|null
     */

    public function getSendNameAttribute()
    {
        return $this->type === self::TYPE_USER ? $this->user_nickname : $this->admin_nickname;
    }

    /**
     * @return string|null
     */

    public function getSendUsernameAttribute()
    {
        return $this->type === self::TYPE_USER ? $this->user_username : $this->admin_username;
    }
}
