<?php

Route::post('/login', ['uses' => 'AdminController@login']);

//图片上传地址
Route::get('/upload-url', ['uses' => 'BaseInfoController@getUploadUrl']);

Route::group(['middleware' => ['auth:admin']], function () {

    //当前用户详情
    Route::get('/user', ['uses' => 'AdminController@getCurrentInfo']);

    //修改当前用户密码
    Route::put('/change-password', ['uses' => 'AdminController@putCurrentPassword']);

    //退出登录
    Route::post('/', ['uses' => 'AdminController@logout']);

    /**
     * 下拉选择列表
     */
    Route::group(['prefix' => 'select'], function () {

        //广告位置
        Route::get('/advertisement-position', ['uses' => 'AdvertisementController@getPositionList']);

        //广告位置
        Route::get('/advertisement-contact-type', ['uses' => 'AdvertisementController@getContactTypeList']);

        //规格分类
        Route::get('/specification-category', ['uses' => 'SpecificationController@getSpecificationCategoryList']);

        //规格组合后列表
        Route::get('/specification-combination', ['uses' => 'SpecificationController@getCombinationList']);

        //顶级产品分类
        Route::get('/top-product-category', ['uses' => 'ProductCategoryController@getTopProductCategory']);

        //产品分类推荐定义
        Route::get('/product-category-recommend', ['uses' => 'ProductCategoryController@getRecommendList']);

        //树形的产品分类
        Route::get('/product-category-tree', ['uses' => 'ProductCategoryController@getTreeList']);

        //推荐产品选项
        Route::get('/product-recommend', ['uses' => 'ProductController@getRecommendDefinition']);

        //热门产品选项
        Route::get('/product-hot', ['uses' => 'ProductController@getHotDefinition']);

        //新产品选项
        Route::get('/product-new', ['uses' => 'ProductController@getNewDefinition']);

        //产品标签列表
        Route::get('/tags/product', ['uses' => 'TagController@getProductList']);

        //合伙人标签列表
        Route::get('/tags/partner', ['uses' => 'TagController@getPartnerList']);

        //标签类型列表
        Route::get('/tags-type', ['uses' => 'TagController@getTypeList']);

        //状态下拉列表
        Route::get('/status', ['uses' => 'BaseInfoController@getStatusList']);

        //角色选择框
        Route::get('/roles', ['uses' => 'RoleController@getEnableList']);

        //权限列表
        Route::get('/permissions', ['uses' => 'BaseInfoController@getPermissionList']);

        //管理员类型
        Route::get('/administrator-type', ['uses' => 'AdminController@getTypeList']);

        //发票抬头类型
        Route::get('/invoices/type', ['uses' => 'InvoiceController@getTypeList']);

        //发票类型
        Route::get('/invoices/invoice-type', ['uses' => 'InvoiceController@getInvoiceTypeList']);

        //发票材质类型
        Route::get('/invoices/material-type', ['uses' => 'InvoiceController@getMaterialTypeTextList']);

        //合伙人类型
        Route::get('/partner-type', ['uses' => 'PartnerController@getTypeList']);

        //合伙人状态
        Route::get('/partner-status', ['uses' => 'PartnerController@getStatusList']);


    });

    /**
     * 设置模块√
     */
    Route::group(['prefix' => 'settings'], function () {

        //读取设置
        Route::get('/', ['uses' => 'BaseInfoController@getSetting']);

        //保存设置
        Route::put('/', ['uses' => 'BaseInfoController@saveSetting']);
    });

    /**
     * 标签管理√
     */

    Route::group(['prefix' => 'tags'], function () {

        //标签列表
        Route::get('/', ['uses' => 'TagController@getList']);

        //新增标签
        Route::post('/', ['uses' => 'TagController@postInfo']);

        //标签详情
        Route::get('/{code}', ['uses' => 'TagController@getInfo']);

        //修改标签
        Route::put('/{code}', ['uses' => 'TagController@putInfo']);

        //删除标签
        Route::delete('/{code}', ['uses' => 'TagController@deleteInfo']);

        //禁用标签
        Route::put('/{code}/disabled', ['uses' => 'TagController@putDisabled']);

        //启用标签
        Route::put('/{code}/enabled', ['uses' => 'TagController@putEnabled']);
    });

    /**
     * 管理员模块√
     */
    Route::group(['prefix' => 'administrators'], function () {

        //列表
        Route::get('/', ['uses' => 'AdminController@getList']);

        //新增
        Route::post('/', ['uses' => 'AdminController@postInfo']);

        //详情
        Route::get('/{code}', ['uses' => 'AdminController@getInfo']);

        //修改
        Route::put('/{code}', ['uses' => 'AdminController@putInfo']);

        Route::delete('/{code}', ['uses' => 'AdminController@deleteInfo']);

        //启用
        Route::put('/{code}/enabled', ['uses' => 'AdminController@putStatusEnable']);

        //禁用
        Route::put('/{code}/disabled', ['uses' => 'AdminController@putStatusDisable']);

        //重置密码
        Route::put('/{code}/reset-password', ['uses' => 'AdminController@putResetPassword']);

    });

    /**
     * 角色模块√
     */
    Route::group(['prefix' => 'roles'], function () {

        //列表
        Route::get('/', ['uses' => 'RoleController@getList']);

        //新增
        Route::post('/', ['uses' => 'RoleController@postInfo']);

        //详情
        Route::get('/{code}', ['uses' => 'RoleController@getInfo']);

        //修改
        Route::put('/{code}', ['uses' => 'RoleController@putInfo']);

        //删除
        Route::delete('/{code}', ['uses' => 'RoleController@deleteInfo']);

        //启用
        Route::put('/{code}/enabled', ['uses' => 'RoleController@putStatusEnable']);

        //禁用
        Route::put('/{code}/disabled', ['uses' => 'RoleController@putStatusDisable']);

    });

    /**
     * 用户模块√
     */
    Route::group(['prefix' => 'users'], function () {

        //列表
        Route::get('/', ['uses' => 'UserController@getList']);

        //启用
        Route::put('/{code}/enable', ['uses' => 'UserController@putStatusEnable']);

        //禁用
        Route::put('/{code}/disable', ['uses' => 'UserController@putStatusDisable']);
    });

    /**
     * 产品分类√
     */
    Route::group(['prefix' => 'product-categories'], function () {

        //列表
        Route::get('/', ['uses' => 'ProductCategoryController@getList']);

        //新增
        Route::post('/', ['uses' => 'ProductCategoryController@postInfo']);

        //修改
        Route::put('/{code}', ['uses' => 'ProductCategoryController@putInfo']);

        //详情
        Route::get('/{code}', ['uses' => 'ProductCategoryController@getInfo']);

        //删除
        Route::delete('/{code}', ['uses' => 'ProductCategoryController@deleteInfo']);

        //启用
        Route::put('/{code}/enable', ['uses' => 'ProductCategoryController@putEnable']);

        //禁用
        Route::put('/{code}/disable', ['uses' => 'ProductCategoryController@putDisable']);

    });

    /**
     * 规格√
     */
    Route::group(['prefix' => 'specification'], function () {

        //列表
        Route::get('/', ['uses' => 'SpecificationController@getList']);

        //新增
        Route::post('/', ['uses' => 'SpecificationController@postInfo']);

        //修改
        Route::put('/{code}', ['uses' => 'SpecificationController@putInfo']);

        //详情
        Route::get('/{code}', ['uses' => 'SpecificationController@getInfo']);

        //删除
        Route::delete('/{code}', ['uses' => 'SpecificationController@deleteInfo']);

        //启用
        Route::put('/{code}/enable', ['uses' => 'SpecificationController@putEnable']);

        //禁用
        Route::put('/{code}/disable', ['uses' => 'SpecificationController@putDisable']);

    });

    /**
     * 产品√
     */
    Route::group(['prefix' => 'product'], function () {

        //列表
        Route::get('/', ['uses' => 'ProductController@getList']);

        //新增
        Route::post('/', ['uses' => 'ProductController@postInfo']);

        //软删除的产品列表
        Route::get('/recycle-list', ['uses' => 'ProductController@getRecycleList']);

        //详情
        Route::get('/{code}', ['uses' => 'ProductController@getInfo']);

        //获取组合列表
        Route::get('/{code}/specification-combination', ['uses' => 'ProductController@getSpecificationCombination']);

        //获取组合详情
        Route::get('/{code}/specification-combination/{specificationCode}', ['uses' => 'ProductController@getSpecificationCombinationInfo']);

        //修改组合信息
        Route::put('/{code}/specification-combination/{specificationCode}', ['uses' => 'ProductController@putSpecificationCombinationInfo']);

        //修改
        Route::put('/{code}', ['uses' => 'ProductController@putInfo']);

        //删除
        Route::delete('/{code}', ['uses' => 'ProductController@deleteInfo']);

        //启用
        Route::put('/{code}/enable', ['uses' => 'ProductController@putEnable']);

        //禁用
        Route::put('/{code}/disable', ['uses' => 'ProductController@putDisable']);

        //读取标签设置
        Route::get('/{code}/tags', ['uses' => 'ProductController@getTags']);

        //写入标签设置
        Route::put('/{code}/tags', ['uses' => 'ProductController@putTags']);

        //恢复删除产品
        Route::put('/{code}/restore', ['uses' => 'ProductController@restoreInfo']);
    });

    /**
     * 广告√
     */
    Route::group(['prefix' => 'advertisement'], function () {

        //列表
        Route::get('/', ['uses' => 'AdvertisementController@getList']);

        //新增
        Route::post('/', ['uses' => 'AdvertisementController@postInfo']);

        //修改
        Route::put('/{code}', ['uses' => 'AdvertisementController@putInfo']);

        //详情
        Route::get('/{code}', ['uses' => 'AdvertisementController@getInfo']);

        //删除
        Route::delete('/{code}', ['uses' => 'AdvertisementController@deleteInfo']);

        //启用
        Route::put('/{code}/enable', ['uses' => 'AdvertisementController@putStatusEnable']);

        //禁用
        Route::put('/{code}/disable', ['uses' => 'AdvertisementController@putStatusDisable']);

    });

    /**
     * 合伙人模块
     */
    Route::group(['prefix' => 'partners'], function () {

        //列表
        Route::get('/', ['uses' => 'PartnerController@getList']);

        //新增
        Route::post('/', ['uses' => 'PartnerController@postInfo']);

        //详情
        Route::get('/{code}', ['uses' => 'PartnerController@getInfo']);

        //修改
        Route::put('/{code}', ['uses' => 'PartnerController@putInfo']);

        //删除
        Route::delete('/{code}', ['uses' => 'PartnerController@deleteInfo']);

        //禁用
        Route::put('/{code}/disabled', ['uses' => 'PartnerController@putDisabled']);

        //启用
        Route::put('/{code}/enabled', ['uses' => 'PartnerController@putEnabled']);

        //获取绑定标签
        Route::get('/{code}/tags', ['uses' => 'PartnerController@getTags']);

        //设置绑定标签
        Route::put('/{code}/tags', ['uses' => 'PartnerController@putTags']);

        //一类降级二类
        Route::put('/{code}/downgrade', ['uses' => 'PartnerController@putDowngrade']);
    });

    /**
     * 合伙人佣金规则模块√
     */

    Route::group(['prefix' => 'commission-rules'], function () {

        //列表
        Route::get('/', ['uses' => 'CommissionRuleController@getList']);

        //新增
        Route::post('/', ['uses' => 'CommissionRuleController@postInfo']);

        //详情
        Route::get('/{code}', ['uses' => 'CommissionRuleController@getInfo']);

        //修改
        Route::put('/{code}', ['uses' => 'CommissionRuleController@putInfo']);

        //删除
        Route::delete('/{code}', ['uses' => 'CommissionRuleController@deleteInfo']);

        //禁用
        Route::put('/{code}/disabled', ['uses' => 'CommissionRuleController@putDisabled']);

        //启用
        Route::put('/{code}/enabled', ['uses' => 'CommissionRuleController@putEnabled']);

    });

    /**
     * 产品评价管理
     */
    Route::group(['prefix' => 'evaluations'], function () {

        //
        Route::get('/', ['uses' => 'EvaluationController@getAdminList']);

    });

    /**
     * 发票管理√
     */
    Route::group(['prefix' => 'invoices'], function () {

        //发票列表
        Route::get('/', ['uses' => 'InvoiceController@getList']);

        //获取详情
        Route::get('/{code}', ['uses' => 'InvoiceController@getInfo']);

        //修改发票详情
        Route::put('/{code}', ['uses' => 'InvoiceController@putInfo']);

        //开票
        Route::put('/{code}/begin', ['uses' => 'InvoiceController@putBegin']);

        //确认
        Route::put('/{code}/confirm', ['uses' => 'InvoiceController@putConfirm']);

    });


    /**
     * 提现
     */
    Route::group(['prefix' => 'withdraw'], function () {

        //获取所有提现记录
        Route::get('/', ['uses' => 'PartnerWithdrawController@getAllList']);


    });

    /**
     * 客服模块√
     */

    Route::group(['prefix' => 'customer-service'], function () {

        //
        Route::get('/clients', ['uses' => 'CustomerServiceMessageController@getClientList']);

        //
        Route::get('/clients/{code}/messages', ['uses' => 'CustomerServiceMessageController@getClientMessageList']);

        //
        Route::delete('/clients/{code}/unread', ['uses' => 'CustomerServiceMessageController@clearUnreadNum']);

        //
        Route::post('/clients/{code}/message', ['uses' => 'CustomerServiceMessageController@postClientMessage']);

        //
        Route::post('/clients/{code}/image', ['uses' => 'CustomerServiceMessageController@postClientImage']);

        //
        Route::get('/messages', ['uses' => 'CustomerServiceMessageController@getAllList']);

    });

    /**
     * 订单模块
     */
    Route::group(['prefix' => 'order'], function () {

        //所有订单列表
        Route::get('/', ['uses' => 'OrderController@getAllList']);

        //获取订单详情
        Route::get('/{code}', ['uses' => 'OrderController@getAdminInfo']);

        //确认支付
        Route::put('/{code}/to-pay', ['uses' => 'OrderController@putToPay']);

        //获取今天总金额
        Route::get('/today-amount', ['uses' => 'OrderController@getTodayAmount']);

        //确认发货
        Route::put('/{code}/confirm-delivery', ['uses' => 'OrderController@putConfirmDelivery']);

        //录入快递单号
        Route::put('/{code}/tracking-number', ['uses' => 'OrderController@putTrackingNumber']);
    });

    /**
     * 退货单模块
     */
    Route::group(['prefix' => 'refund-orders'], function () {

        //列表
        Route::get('/', ['uses' => 'RefundOrderController@getAllList']);

        //获取详情
        Route::get('/{code}', ['uses' => 'RefundOrderController@getInfo']);

        //审核
        Route::put('/{code}/audit', ['uses' => 'RefundOrderController@putAudit']);

        //确认放款
        Route::put('/{code}/refund', ['uses' => 'RefundOrderController@putRefund']);

        //确认发货
        Route::put('/{code}/delivery', ['uses' => 'RefundOrderController@putDelivery']);

    });

    /**
     * 文章管理模块
     */

    Route::group(['prefix' => 'articles'], function () {

        //列表
        Route::get('/', ['uses' => 'ArticleController@getList']);

        //新增
        Route::post('/', ['uses' => 'ArticleController@postInfo']);

        //详情
        Route::get('/{code}', ['uses' => 'ArticleController@getInfo']);

        //修改
        Route::put('/{code}', ['uses' => 'ArticleController@putInfo']);

        //删除
        Route::delete('/{code}', ['uses' => 'ArticleController@deleteInfo']);
    });

    /**
     * 导出模块
     */
    Route::group(['prefix' => 'export'], function () {

        //
        Route::get('/order', ['uses' => 'OrderController@export']);

        //
        Route::get('/partner', ['uses' => 'PartnerController@exportList']);
    });

    /**
     * 导入模块
     */

    Route::group(['prefix' => 'import'], function () {

        //合伙人导入
        Route::post('/partners', ['uses' => 'PartnerController@batchImport']);

    });
});
