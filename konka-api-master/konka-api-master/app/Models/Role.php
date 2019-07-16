<?php

namespace App\Models;


use App\ModelEvents\Roles\DeleteRole;
use App\ModelEvents\Roles\DisabledRole;
use App\ModelEvents\Roles\EnabledRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;
use ZhiEq\Utils\Trigger;


/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $code 编码
 * @property string $name 名称
 * @property int|null $status 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed $permissions
 * @property-read mixed $status_text
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role disabled()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role enabled()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RolePermission[] $rolePermissions
 */
class Role extends Model
{
    use DefinitionAttribute;

    protected $attributes = [
        'status' => PublicDefinition::STATUS_ENABLED
    ];

    protected $hidden = [
        'rolePermissions'
    ];

    protected $appends = [
        'status_text'
    ];

    /**
     * @var array
     */

    protected $_permissions = [];

    /**
     *
     */

    protected static function boot()
    {
        parent::boot();
        self::creating(function (self $model) {
            $model->code = CodeGenerator::getUniqueCode(self::class, function () {
                return self::maxCode();
            }, 6, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::codePrefix(), 0);
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
        return 'ROLE';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'role_code', 'code');
    }

    /**
     * @return mixed
     */

    public function getPermissionsAttribute()
    {
        return $this->rolePermissions->pluck('permission')->toArray();
    }

    /**
     * @param $value
     */

    public function setPermissionsAttribute($value)
    {
        $this->_permissions = $value;
    }

    /**
     * @return mixed
     */

    public function getStatusTextAttribute()
    {
        return PublicDefinition::getStatusLabel($this->status);
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
     * @return bool
     */

    public function toDelete()
    {
        return Trigger::eventWithTransaction(new DeleteRole($this));
    }

    /**
     * @return bool
     */

    public function toDisabled()
    {
        return Trigger::eventWithTransaction(new DisabledRole($this));
    }

    /**
     * @return bool
     */

    public function toEnabled()
    {
        return Trigger::eventWithTransaction(new EnabledRole($this));
    }
}
