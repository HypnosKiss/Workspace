<?php

//获取小程序当前用户openId
Route::get('/{code}/openid', ['uses' => 'UserController@getOpenId']);

//微信openid登录
Route::post('/wx-openid-login', ['uses' => 'UserController@wxOpenidLogin'])->middleware(['wxAppletsSessionAutoInit']);

//微信小程序手机号码登录
//Route::post('/wx-applets-login', ['uses' => 'UserController@wxAppletsLogin'])->middleware(['wxAppletsSessionAutoInit']);

//上架产品列表
Route::get('/product-list', ['uses' => 'ProductController@getEnableList']);

//搜索
Route::get('/search-product-list', ['uses' => 'ProductController@searchEnableList']);

//获取用户五条搜索记录
Route::get('/search-record', ['uses' => 'SearchRecordController@getSearchRecord']);

//获取5个热门搜索记录
Route::get('/popular-search-record', ['uses' => 'SearchRecordController@getPopularSearchRecord']);

//热销产品列表
Route::get('/hot-product-list', ['uses' => 'ProductController@getHotList']);

//推荐产品列表
Route::get('/recommend-product-list', ['uses' => 'ProductController@getRecommendList']);

//新产品列表
Route::get('/new-product-list', ['uses' => 'ProductController@getNewList']);

//产品详情
Route::get('/product/{code}', ['uses' => 'ProductController@getInfo']);

//启用产品分类列表
Route::get('/product-category-list', ['uses' => 'ProductCategoryController@getEnableList']);

//读取设置
Route::get('/setting', ['uses' => 'BaseInfoController@getSetting']);

//获取通用优惠劵
Route::get('/general-purpose-coupon-list', ['uses' => 'CouponController@getGeneralPurposeList']);

//获取上架5条广告
Route::get('/advertisement', ['uses' => 'AdvertisementController@getEnableList']);

//获取8个子分类
Route::get('/sub-product-category', ['uses' => 'ProductCategoryController@getEightSubProductCategory']);

//图片上传
Route::post('/upload-image', ['uses' => 'BaseInfoController@uploadImage']);

//发送短信验证码
Route::post('/send-sms-code', ['uses' => 'BaseInfoController@sendSmsCode']);

//获取文章详情
Route::get('/articles/{code}', ['uses' => 'ArticleController@getInfo']);

//小程序码
Route::group(['prefix' => 'applets-code'], function () {

    //产品详情小程序码
    Route::get('/{code}/product', ['uses' => 'ProductController@generateAppletsCode']);

});

Route::group(['middleware' => ['auth:api', 'wxAppletsSessionAutoInit']], function () {

    //清除搜索记录
    Route::delete('/search-record', ['uses' => 'SearchRecordController@deleteSearchRecord']);

    /**
     * 用户
     */

    Route::group(['prefix' => 'current'], function () {

        //用户详情
        Route::get('/', ['uses' => 'UserController@getInfo']);

        //微信受权修改用户头像
        Route::put('/', ['uses' => 'UserController@putInfoAvatar']);

    });

    //获取海报
    Route::get('/product-poster/{code}', ['uses' => 'ProductController@getProductPoster']);

    /**
     * 地址
     */

    Route::group(['prefix' => 'addresses'], function () {

        //列表
        Route::get('/', ['uses' => 'UserAddressesController@getList']);

        //新增
        Route::post('/', ['uses' => 'UserAddressesController@postInfo']);

        //修改
        Route::put('/{code}', ['uses' => 'UserAddressesController@putInfo']);

        //详情
        Route::get('/{code}', ['uses' => 'UserAddressesController@getInfo']);

        //删除
        Route::delete('/{code}', ['uses' => 'UserAddressesController@deleteInfo']);

        //修改默认状态
        Route::put('/{code}/status', ['uses' => 'UserAddressesController@putInfoStatus']);
    });

    /**
     * 发票
     */

    Route::group(['prefix' => 'invoice'], function () {

        //列表
        Route::get('/', ['uses' => 'UserInvoiceController@getList']);

        //新增
        Route::post('/', ['uses' => 'UserInvoiceController@postInfo']);

        //修改
        Route::put('/{code}', ['uses' => 'UserInvoiceController@putInfo']);

        //详情
        Route::get('/{code}', ['uses' => 'UserInvoiceController@getInfo']);

        //删除
        Route::delete('/{code}', ['uses' => 'UserInvoiceController@deleteInfo']);

        //修改默认状态
        Route::put('/{code}/default', ['uses' => 'UserInvoiceController@putInfoStatus']);
    });

    /**
     * 订单
     */
    Route::group(['prefix' => 'order'], function () {

        //列表
        Route::get('/', ['uses' => 'OrderController@getList']);

        //新增
        Route::post('/', ['uses' => 'OrderController@postInfo']);

        //详情
        Route::get('/{code}', ['uses' => 'OrderController@getInfo']);

        //支付订单
        Route::get('/pay/{code}', ['uses' => 'OrderController@payInfo']);

        //删除订单
        Route::delete('/{code}', ['uses' => 'OrderController@deleteInfo']);

        //取消订单
        Route::put('/{code}/cancel', ['uses' => 'OrderController@cancelInfo']);

        //确认收货
        Route::put('/{code}/confirm-receipt', ['uses' => 'OrderController@confirmReceipt']);

        //获取订单状态
        Route::get('/{code}/status', ['uses' => 'OrderController@getOrderStatus']);
    });

    /**
     * 退货单
     */
    Route::group(['prefix' => 'refund-order'], function () {

        //列表
        Route::get('/', ['uses' => 'RefundOrderController@getList']);

        //新增
        Route::post('/', ['uses' => 'RefundOrderController@postInfo']);

        //详情
        Route::get('/{code}', ['uses' => 'RefundOrderController@getInfo']);

        //确认换货收货
        Route::put('/{code}/confirm', ['uses' => 'RefundOrderController@confirmInfo']);
    });

    /**
     * 购物车
     */
    Route::group(['prefix' => 'shopping-cart'], function () {

        //列表
        Route::get('/', ['uses' => 'ShoppingCartController@getList']);

        //新增
        Route::post('/', ['uses' => 'ShoppingCartController@postInfo']);

        //批量删除
        Route::put('/batch-delete', ['uses' => 'ShoppingCartController@batchDelete']);

        //修改
        Route::put('/{id}', ['uses' => 'ShoppingCartController@putInfo']);
    });

    /**
     * 评价
     */
    Route::group(['prefix' => 'evaluation'], function () {

        //获取某个产品评价列表
        Route::get('/{productCode}', ['uses' => 'EvaluationController@getList']);

        //新增评价
        Route::post('/{orderCode}', ['uses' => 'EvaluationController@postInfo']);

        //评价回复
        Route::post('/{code}/reply', ['uses' => 'EvaluationController@postReply']);

        //详情
        Route::get('/{code}/info', ['uses' => 'EvaluationController@getInfo']);
    });

    /**
     * 留言
     */
    Route::group(['prefix' => 'customer-service-message'], function () {

        //获取用户留言
        Route::get('/', ['uses' => 'CustomerServiceMessageController@getUserList']);

        //留言
        Route::post('/', ['uses' => 'CustomerServiceMessageController@postInfo']);
    });

    /**
     * 合伙人模块
     */

    Route::group(['prefix' => 'partner'], function () {

        //手机号码登录合伙人
        Route::post('/mobile-login', ['uses' => 'PartnerController@postMobileLogin']);

        //账号密码登录合伙人
        Route::post('/account-login', ['uses' => 'PartnerController@postAccountLogin']);

        //微信手机登录合伙人
        Route::post('/wx-mobile-login', ['uses' => 'PartnerController@postWxPhoneLogin']);

        //微信手机登录合伙人
        Route::post('/reset-password', ['uses' => 'PartnerController@resetPassword']);

        //激活码注册合伙人
        Route::post('/active-code-register', ['uses' => 'PartnerController@postActiveCodeRegister']);

        //校验激活码
        Route::get('/active-code/{code}', ['uses' => 'PartnerController@getActiveCodeName']);

        //退出登录
        Route::put('/logout', ['uses' => 'PartnerController@putLogout']);

        //分销订单
        Route::get('/sale-commission-orders', ['uses' => 'PartnerController@getSaleCommissionOrders']);

        //销售额
        Route::get('/sales', ['uses' => 'PartnerController@getSales']);

        //K币收益记录
        Route::get('/sale-commission-records', ['uses' => 'PartnerController@getSaleCommissionRecords']);

        //分销订单统计
        Route::get('/sale-commission-order-total', ['uses' => 'PartnerController@getSaleCommissionOrderTotal']);

        //销售额统计
        Route::get('/sales-total', ['uses' => 'PartnerController@getSaleTotal']);

        //K币收益统计
        Route::get('/sale-commission-record-total', ['uses' => 'PartnerController@getSaleCommissionRecordTotal']);

    });

});
