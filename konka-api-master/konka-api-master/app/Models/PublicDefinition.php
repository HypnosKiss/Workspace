<?php

namespace App\Models;

use ZhiEq\Traits\DefinitionAttribute;

/**
 * Class PublicDefinition
 * @package App\Models
 * @method static array getSwitchLabels()
 * @method static array getSwitchList()
 * @method static string getSwitchLabel($key)
 * @method static array getStatusLabels()
 * @method static array getStatusList()
 * @method static string getStatusLabel($key)
 * @method static array getPermissionLabels()
 * @method static array getPermissionList()
 * @method static string getPermissionLabel($key)
 * @method static array getInvoiceTypeLabels()
 * @method static array getInvoiceTypeList()
 * @method static string getInvoiceTypeLabel($key)
 */
class PublicDefinition
{
    use DefinitionAttribute;

    /**
     * 开关选择
     */

    const SWITCH_YES = 10;
    const SWITCH_NO = 20;

    public static function switchDefinition()
    {
        return [
            self::SWITCH_YES => '是',
            self::SWITCH_NO => '否'
        ];
    }

    /**
     * 简单状态选择
     */

    const STATUS_ENABLED = 10;
    const STATUS_DISABLED = 20;

    public static function statusDefinition()
    {
        return [
            self::STATUS_ENABLED => '启用',
            self::STATUS_DISABLED => '禁用'
        ];
    }

    /**
     * 发票抬头类型
     */

    const INVOICE_TYPE_PERSONAL = 10;
    const INVOICE_TYPE_COMPANY = 20;

    public static function invoiceTypeDefinition()
    {
        return [
            self::INVOICE_TYPE_PERSONAL => '个人',
            self::INVOICE_TYPE_COMPANY => '公司'
        ];
    }

    /**
     * 权限列表
     */

    const PERMISSION_CREATE_PRODUCT = 'createProduct';
    const PERMISSION_UPDATE_PRODUCT = 'updateProduct';

    public static function permissionDefinition()
    {
        return [
            self::PERMISSION_CREATE_PRODUCT => '创建商品',
            self::PERMISSION_UPDATE_PRODUCT => '更新商品',
        ];
    }
}
