<?php

namespace App\Models;


use App\ModelEvents\Admin\DeleteAdmin;
use App\ModelEvents\Admin\DisabledAdmin;
use App\ModelEvents\Admin\EnabledAdmin;
use Hash;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;
use ZhiEq\Utils\Trigger;


/**
 * App\Models\Admin
 *
 * @property int $id
 * @property string $code 编码
 * @property string $username 用户名
 * @property string $password 密码
 * @property string $nickname 昵称
 * @property string|null $avatar 头像
 * @property int $type 类型
 * @property int $status 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AdminPermission[] $adminPermissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AdminRole[] $adminRoles
 * @property-read null|string $avatar_url
 * @property-read array $permissions
 * @property-read mixed $status_text
 * @property-read mixed $type_text
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin disabled()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin enabled()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin whereUsername($value)
 * @method static getTypeList()
 * @method static getTypeLabels()
 * @method static getTypeLabel($key)
 * @mixin \Eloquent
 * @property-read array $roles
 */
class Admin extends Model implements Authenticatable
{
    use DefinitionAttribute;

    /**
     * 管理员类型定义
     */

    const TYPE_SUPER_ADMINISTRATOR = 10;
    const TYPE_ADMINISTRATOR = 20;

    protected static function typeDefinition()
    {
        return [
            self::TYPE_SUPER_ADMINISTRATOR => '超级管理员',
            self::TYPE_ADMINISTRATOR => '普通管理员'
        ];
    }

    protected $attributes = [
        'status' => PublicDefinition::STATUS_ENABLED
    ];

    protected $appends = [
        'avatar_url', 'status_text', 'type_text'
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function (self $model) {
            $model->code = CodeGenerator::getNext(self::maxCode(), 6, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::codePrefix(), 0);
        });
    }

    /**
     * @return bool|int|string
     */

    protected static function maxCode()
    {
        if ($max = self::orderByDesc('code')->first()) {
            return substr($max->code, strlen(self::codePrefix()));
        }
        return 0;
    }

    /**
     * @return string
     */

    protected static function codePrefix()
    {
        return 'AM';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function adminPermissions()
    {
        return $this->hasMany('App\Models\AdminPermission', 'admin_code', 'code');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function adminRoles()
    {
        return $this->hasMany('App\Models\AdminRole', 'admin_code', 'code');
    }

    /**
     * @return null|string
     */

    public function getAvatarUrlAttribute()
    {
        return empty($this->avatar) ? null : upload_file_to_url($this->avatar);
    }

    /**
     * @return mixed
     */

    public function getStatusTextAttribute()
    {
        return PublicDefinition::getStatusLabel($this->status);
    }

    /**
     * @return mixed
     */

    public function getTypeTextAttribute()
    {
        return self::getTypeLabel($this->type);
    }

    /**
     * @return array
     */

    public function getPermissionsAttribute()
    {
        return $this->adminPermissions->pluck('permission')->toArray();
    }

    /**
     * @return array
     */

    public function getRolesAttribute()
    {
        return $this->adminRoles->pluck('role_code')->toArray();
    }

    /**
     * @param Builder $query
     * @return mixed
     */

    public function scopeEnabled($query)
    {
        return $query->where('status', PublicDefinition::STATUS_ENABLED);
    }

    /**
     * @param Builder $query
     * @return mixed
     */

    public function scopeDisabled($query)
    {
        return $query->where('status', PublicDefinition::STATUS_DISABLED);
    }

    /**
     * @param $password
     * @return bool
     */

    public function validatePassword($password)
    {
        return Hash::check($password, $this->password);
    }

    /**
     * @param $password
     * @return mixed
     */

    public function changePassword($password)
    {
        return $this->setAttribute('password', Hash::make($password))->save();
    }

    /**
     * @return bool
     */

    public function toEnabled()
    {
        return Trigger::eventWithTransaction(new EnabledAdmin($this));
    }

    /**
     * @return bool
     */

    public function toDisabled()
    {
        return Trigger::eventWithTransaction(new DisabledAdmin($this));
    }

    /**
     * @return bool
     */

    public function toDelete()
    {
        return Trigger::eventWithTransaction(new DeleteAdmin($this));
    }


    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'code';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return null;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {

    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return null;
    }
}
