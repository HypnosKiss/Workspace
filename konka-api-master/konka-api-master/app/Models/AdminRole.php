<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdminRole
 *
 * @property int $id
 * @property string $admin_code 管理员编码
 * @property string $role_code 角色编码
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Role $role
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminRole whereAdminCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminRole whereRoleCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminRole whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $status 状态
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminRole whereStatus($value)
 */
class AdminRole extends Model
{
    protected $attributes = [
        'status' => PublicDefinition::STATUS_ENABLED
    ];
}
