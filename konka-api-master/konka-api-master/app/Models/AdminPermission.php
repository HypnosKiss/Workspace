<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdminPermission
 *
 * @property int $id
 * @property string $admin_code 管理员编码
 * @property string $permission 权限
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminPermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminPermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminPermission query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminPermission whereAdminCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminPermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminPermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminPermission wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AdminPermission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdminPermission extends Model
{

}