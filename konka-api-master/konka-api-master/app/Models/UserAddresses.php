<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;

/**
 * App\Models\UserAddresses
 *
 * @property int $id
 * @property string $code
 * @property string $user_code 用户编码
 * @property string $name 收货人名称
 * @property string $phone 收货人电话
 * @property string|null $province_code 省编码
 * @property string $province_text 省中文
 * @property string|null $city_code 市编码
 * @property string $city_text 市中文
 * @property string|null $county_code 县编码
 * @property string $county_text 县中文
 * @property string $address 详细地址
 * @property string|null $postal_code 邮政编码
 * @property int|null $status 状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $full_addresses
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses whereCityCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses whereCityText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses whereCountyCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses whereCountyText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses whereProvinceCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses whereProvinceText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserAddresses whereUserCode($value)
 * @mixin \Eloquent
 * @method static getStatusList()
 * @method static getStatusLabels()
 * @method static getStatusLabel($key)
 */
class UserAddresses extends Model
{
    use DefinitionAttribute;

    const STATUS_DEFAULT = 10;
    const STATUS_NO_DEFAULT = 20;

    protected $appends = [
        'full_addresses'
    ];


    protected static function boot()
    {
        parent::boot();

        self::creating(function (self $model) {
            $model->code = CodeGenerator::getNext($model->maxCode(), 3, CodeGenerator::TYPE_NUMBER_AND_LETTER, $model->user_code, 0);
        });
    }

    /**
     *
     * @return bool|int|string
     */

    protected function maxCode()
    {
        if ($maxModel = self::where('code', 'like', $this->user_code . '%')->orderByDesc('code')->first()) {
            return substr($maxModel['code'], strlen($this->user_code));
        }
        return 0;
    }

    /**
     * @return array
     */

    protected static function statusDefinition()
    {
        return [
            self::STATUS_DEFAULT => '默认',
            self::STATUS_NO_DEFAULT => '不是默认'
        ];
    }

    /**
     * @return string
     */

    public function getFullAddressesAttribute()
    {
        return $this->province_text . $this->city_text . $this->county_text . $this->address;
    }
}