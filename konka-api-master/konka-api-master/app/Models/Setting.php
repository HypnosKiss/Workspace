<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ZhiEq\Traits\DefinitionAttribute;
use ZhiEq\Utils\CodeGenerator;

/**
 * App\Models\Setting
 *
 * @property int $id
 * @property string $code
 * @property int $type
 * @property string|null $key
 * @property string|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Setting whereValue($value)
 * @method static array getSettingKeyLabels()
 * @method static string getSettingKeyLabel($key)
 * @method static array getSettingKeyList()
 * @method static array getSettingDefaultLabels()
 * @method static string getSettingDefaultLabel($key)
 * @method static array getSettingDefaultList()
 * @mixin \Eloquent
 */
class Setting extends Model
{
    use DefinitionAttribute;

    const TYPE_SETTING = 10;

    /**
     * 合伙人相关设置
     */
    const SETTING_KEY_PARTNER_PROTOCOL = 'partnerProtocol';
    const SETTING_KEY_PARTNER_FIRST_PERCENTAGE = 'partnerFirstPercentage';
    const SETTING_KEY_PARTNER_SECOND_PERCENTAGE = 'partnerSecondPercentage';
    const SETTING_KEY_IS_ALLOW_SELF_REGISTER_PARTNER = 'isAllowSelfRegisterPartner';
    const SETTING_KEY_IS_ALLOW_RECOMMEND_REGISTER_PARTNER = 'isAllowRecommendRegisterPartner';
    const SETTING_KEY_PARTNER_EXCHANGE_PROPORTION = 'partnerExchangeProportion';
    const SETTING_KEY_PARTNER_AMOUNT_TO_INTEGRAL_RATIO = 'partnerAmountToIntegralRatio';

    /**
     * 商品相关设置
     */

    const SETTING_KEY_PRODUCT_WARRANTY_PERIOD = 'productWarrantyPeriod';

    /**
     * 订单相关设置
     */

    const SETTING_KEY_ORDER_WX_PAY_LIMIT_AMOUNT = 'orderWxPayLimitAmount';
    const SETTING_KEY_ORDER_PAY_BANK_NAME = 'orderPayBankName';
    const SETTING_KEY_ORDER_PAY_BANK_ACCOUNT = 'orderPayBankAccount';
    const SETTING_KEY_ORDER_PAY_BANK_OPEN = 'orderPayBankOpen';

    /**
     * 其他相关设置
     */

    const SETTING_KEY_HELP_CENTER_ARTICLE_CODE = 'helpCenterArticleCode';

    /**
     * 全局转发配置
     */

    const SETTING_KEY_GLOBAL_SHARE_TITLE = 'globalShareTitle';
    const SETTING_KEY_GLOBAL_SHARE_IMAGE = 'globalShareImage';

    /**
     * 界面显示设置
     */

    const SETTING_KEY_UI_INDEX_HOT_TITLE = 'uiIndexHotTitle';
    const SETTING_KEY_UI_INDEX_NEW_TITLE = 'uiIndexNewTitle';
    const SETTING_KEY_UI_INDEX_POPULARITY_TITLE = 'uiIndexPopularityTitle';

    /**
     * @return array
     */

    protected static function settingKeyDefinition()
    {
        return [
            self::SETTING_KEY_PARTNER_PROTOCOL => '合伙人协议',
            self::SETTING_KEY_PARTNER_FIRST_PERCENTAGE => '一级合伙人佣金',
            self::SETTING_KEY_PARTNER_SECOND_PERCENTAGE => '二级合伙人佣金',
            self::SETTING_KEY_PARTNER_EXCHANGE_PROPORTION => 'K币兑换比例',
            self::SETTING_KEY_IS_ALLOW_SELF_REGISTER_PARTNER => '是否允许自主注册合伙人',
            self::SETTING_KEY_IS_ALLOW_RECOMMEND_REGISTER_PARTNER => '是否允许推荐注册合伙人',
            self::SETTING_KEY_ORDER_WX_PAY_LIMIT_AMOUNT => '微信支付最高限额',
            self::SETTING_KEY_ORDER_PAY_BANK_NAME => '订单大额收款户名',
            self::SETTING_KEY_ORDER_PAY_BANK_ACCOUNT => '订单大额收款账号',
            self::SETTING_KEY_ORDER_PAY_BANK_OPEN => '订单大额收款开户行',
            self::SETTING_KEY_PARTNER_AMOUNT_TO_INTEGRAL_RATIO => '订单金额兑换K币比例',
            self::SETTING_KEY_HELP_CENTER_ARTICLE_CODE => '帮助中心调取文章编码',
            self::SETTING_KEY_GLOBAL_SHARE_TITLE => '全局转发显示标题',
            self::SETTING_KEY_GLOBAL_SHARE_IMAGE => '全局转发显示图片',
            self::SETTING_KEY_UI_INDEX_HOT_TITLE => '首页热卖单品标题图片(750px*96px)',
            self::SETTING_KEY_UI_INDEX_NEW_TITLE => '首页新品推荐标题图片(750px*96px)',
            self::SETTING_KEY_UI_INDEX_POPULARITY_TITLE => '首页人气榜单标题图片(750px*96px)',
        ];
    }

    /**
     * @return array
     */

    protected static function settingDefaultDefinition()
    {
        return [
            self::SETTING_KEY_PARTNER_PROTOCOL => '',
            self::SETTING_KEY_PARTNER_AMOUNT_TO_INTEGRAL_RATIO => 1,
            self::SETTING_KEY_PARTNER_FIRST_PERCENTAGE => 0,
            self::SETTING_KEY_PARTNER_SECOND_PERCENTAGE => 0,
            self::SETTING_KEY_PARTNER_EXCHANGE_PROPORTION => 100,
            self::SETTING_KEY_IS_ALLOW_SELF_REGISTER_PARTNER => PublicDefinition::SWITCH_NO,
            self::SETTING_KEY_IS_ALLOW_RECOMMEND_REGISTER_PARTNER => PublicDefinition::SWITCH_NO,
            self::SETTING_KEY_ORDER_WX_PAY_LIMIT_AMOUNT => 20000,
            self::SETTING_KEY_ORDER_PAY_BANK_NAME => '深圳康佳电子科技有限公司',
            self::SETTING_KEY_ORDER_PAY_BANK_ACCOUNT => '757571651876',
            self::SETTING_KEY_ORDER_PAY_BANK_OPEN => '中国银行深圳沙河支行',
            self::SETTING_KEY_HELP_CENTER_ARTICLE_CODE => '',
            self::SETTING_KEY_GLOBAL_SHARE_TITLE => '',
            self::SETTING_KEY_GLOBAL_SHARE_IMAGE => '',
            self::SETTING_KEY_UI_INDEX_HOT_TITLE => 'remaidanpin.png',
            self::SETTING_KEY_UI_INDEX_NEW_TITLE => 'xinpintuijian.png',
            self::SETTING_KEY_UI_INDEX_POPULARITY_TITLE => 'renqibangdan.png',
        ];
    }

    /**
     * @return array
     */

    protected static function settingImageKey()
    {
        return [
            self::SETTING_KEY_GLOBAL_SHARE_IMAGE,
            self::SETTING_KEY_UI_INDEX_HOT_TITLE,
            self::SETTING_KEY_UI_INDEX_NEW_TITLE,
            self::SETTING_KEY_UI_INDEX_POPULARITY_TITLE,
        ];
    }

    /**
     * 引导方法，增加模型事件处理绑定
     */

    protected static function boot()
    {
        parent::boot();
        self::creating(function (self $model) {
            $model->code = CodeGenerator::getUniqueCode(self::class, function () {
            }, 5, CodeGenerator::TYPE_NUMBER_AND_LETTER, self::codePrefix());
        });
    }

    /**
     * @return string
     */

    protected static function codePrefix()
    {
        return 'ST';
    }

    /**
     * @return bool|int|string
     */

    protected static function maxCode()
    {
        if ($model = self::orderByDesc('code')->first()) {
            return substr($model['code'], strlen(self::codePrefix()));
        }
        return 0;
    }

    /**
     * @param $value
     * @return false|string
     */

    protected static function valueToString($value)
    {
        return json_encode(['v' => $value]);
    }

    /**
     * @param $string
     * @return mixed
     */

    protected static function stringToValue($string)
    {
        return json_decode($string, true)['v'];
    }

    /**
     * @param $key
     * @return mixed|string|null
     */

    public static function getValue($key)
    {
        if ($setting = self::whereType(self::TYPE_SETTING)->where('key', $key)->first()) {
            return self::stringToValue($setting->value);
        }
        return self::getSettingDefaultLabel($key);
    }

    /**
     * @param array $keys
     * @return \Illuminate\Support\Collection
     */

    public static function batchGetValues(array $keys)
    {
        $settings = self::whereType(self::TYPE_SETTING)->whereIn('key', $keys)->get();
        return collect($keys)->map(function ($key) use ($settings) {
            if ($setting = $settings->where('key', $key)->first()) {
                return self::stringToValue($setting['value']);
            }
            return self::getSettingDefaultLabel($key);
        });
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */

    public static function setValue($key, $value)
    {
        if (!$setting = self::whereType(self::TYPE_SETTING)->where('key', $key)->first()) {
            $setting = (new self())->setAttribute('key', $key)->setAttribute('type', self::TYPE_SETTING);
        }
        return $setting->setAttribute('value', self::valueToString($value))->save();
    }

    /**
     * @param $key
     * @return bool
     */

    public static function isImage($key)
    {
        return in_array($key, self::settingImageKey());
    }
}
