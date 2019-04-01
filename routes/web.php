<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

/*Route::group(['domain' => config('app.admin_domain')], function () {
    //地区
    Route::prefix('sync')->group(function () {
        Route::any('/', 'SyncController@index');
        Route::any('article', 'SyncController@article');
        Route::any('member', 'SyncController@member');
        Route::any('fans', 'SyncController@fans');
        Route::any('riji', 'SyncController@riji');
        Route::any('doctor_say', 'SyncController@doctor_say');
        Route::any('clinic', 'SyncController@clinic');
        Route::any('info', 'SyncController@info');
        Route::any('tag', 'SyncController@tag');
        Route::any('album', 'SyncController@album');
        Route::any('book', 'SyncController@book');
        Route::any('quotations', 'SyncController@quotations');
        Route::any('video', 'SyncController@video');
        Route::any('doctor', 'SyncController@doctor');
        Route::any('gift', 'SyncController@gift');
    });
});*/

//后台的
Route::group(['domain' => config('app.admin_domain')], function () {
    Route::any('login', 'Admin\LoginController@index')->name('login');
    Route::get('loginout', 'Admin\LoginController@loginout');
});

Route::group(['domain' => config('app.admin_domain'), 'namespace' => 'Admin', 'middleware' => ['auth:admin', \App\Http\Middleware\AdminRole::class]], function () {
    Route::get('/', 'IndexController@index');
    Route::get('index', 'IndexController@index');
    Route::get('main', 'IndexController@main');

    //地区
    Route::prefix('area')->group(function () {
        Route::any('', 'AreaController@lists');
    });

    /**
       ***************系统*******************
     */
    Route::group(['namespace' => 'System'], function () {
        //系统设置
        Route::prefix('config')->group(function () {
            Route::any('', 'ConfigController@lists');
            Route::get('add', 'ConfigController@edit');
            Route::any('edit', 'ConfigController@edit');
        });

        //管理员
        Route::prefix('admin_user')->group(function () {
            Route::get('', 'AdminUserController@lists');
            Route::get('lists_ajax', 'AdminUserController@listsAjax');
            Route::get('add', 'AdminUserController@edit');
            Route::any('edit', 'AdminUserController@edit');
            Route::post('delete', 'AdminUserController@delete');
            Route::post('status', 'AdminUserController@status');
            Route::any('my_edit', 'AdminUserController@myEdit');
        });

        //管理员角色
        Route::prefix('admin_role')->group(function () {
            Route::get('', 'AdminRoleController@lists');
            Route::get('lists_ajax', 'AdminRoleController@listsAjax');
            Route::get('add', 'AdminRoleController@edit');
            Route::any('edit', 'AdminRoleController@edit');
            Route::post('status', 'AdminRoleController@status');
            Route::post('delete', 'AdminRoleController@delete');
        });

        //管理员角色权限
        Route::prefix('admin_role_right')->group(function () {
            Route::get('', 'AdminRoleRightController@lists');
            Route::get('lists_ajax', 'AdminRoleRightController@listsAjax');
            Route::get('add', 'AdminRoleRightController@edit');
            Route::any('edit', 'AdminRoleRightController@edit');
            Route::post('delete', 'AdminRoleRightController@delete');
            Route::post('status', 'AdminRoleRightController@status');
            Route::get('get_menu', 'AdminRoleRightController@getMenu');
        });

        //菜单管理
        Route::prefix('menu')->group(function () {
            Route::get('', 'MenuController@lists');
            Route::get('add', 'MenuController@edit');
            Route::any('edit', 'MenuController@edit');
            Route::post('delete', 'MenuController@delete');
            Route::post('status', 'MenuController@status');
        });

        //快递公司
        Route::prefix('express_company')->group(function () {
            Route::get('', 'ExpressCompanyController@lists');
            Route::get('lists_ajax', 'ExpressCompanyController@listsAjax');
            Route::get('add', 'ExpressCompanyController@edit');
            Route::any('edit', 'ExpressCompanyController@edit');
            Route::post('delete', 'ExpressCompanyController@delete');
            Route::post('status', 'ExpressCompanyController@status');
            Route::post('position', 'ExpressCompanyController@position');
        });

        //支付方式
        Route::prefix('payment')->group(function () {
            Route::get('', 'PaymentController@lists');
            Route::get('lists_ajax', 'PaymentController@listsAjax');
            Route::get('add', 'PaymentController@edit');
            Route::any('edit', 'PaymentController@edit');
            Route::post('delete', 'PaymentController@delete');
            Route::post('status', 'PaymentController@status');
            Route::post('position', 'PaymentController@position');
        });

        //礼物
        Route::prefix('gift')->group(function () {
            Route::get('', 'GiftController@lists');
            Route::get('lists_ajax', 'GiftController@listsAjax');
            Route::get('add', 'GiftController@edit');
            Route::any('edit', 'GiftController@edit');
            Route::post('delete', 'GiftController@delete');
            Route::post('status', 'GiftController@status');
            Route::post('position', 'GiftController@position');
        });

        //回播
        Route::prefix('relive')->group(function () {
            Route::get('', 'ReliveController@lists');
            Route::get('lists_ajax', 'ReliveController@listsAjax');
            Route::get('add', 'ReliveController@edit');
            Route::any('edit', 'ReliveController@edit');
            Route::post('delete', 'ReliveController@delete');
            Route::post('isRem', 'ReliveController@isRem');
            Route::post('position', 'ReliveController@position');
            Route::post('password', 'ReliveController@password');
            Route::any('play_url', 'ReliveController@play_url');
            Route::get('link_lists', 'ReliveController@linkLists');  //连麦列表
            Route::get('link_lists_ajax', 'ReliveController@linkListsAjax');
        });


        //消息推送
        Route::prefix('push')->group(function () {
            Route::get('', 'PushController@lists');
            Route::get('lists_ajax', 'PushController@listsAjax');
            Route::get('add', 'PushController@edit');
            Route::any('edit', 'PushController@edit');
            Route::post('delete', 'PushController@delete');
            Route::post('push', 'PushController@push');
        });

        //投诉建议
        Route::prefix('message')->group(function () {
            Route::get('', 'MessageController@lists');
            Route::get('lists_ajax', 'MessageController@listsAjax');
            Route::get('add', 'MessageController@edit');
            Route::any('edit', 'MessageController@edit');
            Route::post('delete', 'MessageController@delete');
            Route::post('status', 'MessageController@status');
        });


        //百度统计
        Route::prefix('statistics')->group(function () {
            Route::get('', 'StatisticsController@lists');
            Route::get('lists_ajax', 'StatisticsController@listsAjax');
            Route::get('meibi_lists', 'StatisticsController@meibi_lists');
            Route::get('meibi_lists_ajax', 'StatisticsController@meibi_listsAjax');
            Route::get('balance_lists', 'StatisticsController@balance_lists');
            Route::get('balance_lists_ajax', 'StatisticsController@balance_listsAjax');
        });
    });

    /**
     ***************工具*******************
     */
    Route::group(['namespace' => 'Tool'], function () {
        //文章分类
        Route::prefix('article_category')->group(function () {
            Route::get('', 'ArticleCategoryController@lists');
            Route::get('add', 'ArticleCategoryController@edit');
            Route::any('edit', 'ArticleCategoryController@edit');
            Route::post('delete', 'ArticleCategoryController@delete');
            Route::post('status', 'ArticleCategoryController@status');
        });

        //文章
        Route::prefix('article')->group(function () {
            Route::get('', 'ArticleController@lists');
            Route::get('lists_ajax', 'ArticleController@listsAjax');
            Route::get('add', 'ArticleController@edit');
            Route::any('edit', 'ArticleController@edit');
            Route::post('delete', 'ArticleController@delete');
            Route::post('push', 'ArticleController@push');
            Route::post('status', 'ArticleController@status');
            Route::post('position', 'ArticleController@position');
        });

        //广告位
        Route::prefix('adv_group')->group(function () {
            Route::get('', 'AdvGroupController@lists');
            Route::get('lists_ajax', 'AdvGroupController@listsAjax');
            Route::get('add', 'AdvGroupController@edit');
            Route::any('edit', 'AdvGroupController@edit');
            Route::post('delete', 'AdvGroupController@delete');
            Route::post('status', 'AdvGroupController@status');
        });

        //广告
        Route::prefix('adv')->group(function () {
            Route::get('', 'AdvController@lists');
            Route::get('lists_ajax', 'AdvController@listsAjax');
            Route::get('add', 'AdvController@edit');
            Route::any('edit', 'AdvController@edit');
            Route::post('delete', 'AdvController@delete');
            Route::post('status', 'AdvController@status');
            Route::post('position', 'AdvController@position');
        });

        //标签管理
        Route::prefix('tag')->group(function () {
            Route::get('', 'TagController@lists');
            Route::get('lists_ajax', 'TagController@listsAjax');
            Route::get('add', 'TagController@edit');
            Route::any('edit', 'TagController@edit');
            Route::post('delete', 'TagController@delete');
            Route::post('status', 'TagController@status');
            Route::post('position', 'TagController@position');

        });

        //大咖说管理
        Route::prefix('yu_video')->group(function () {
            Route::get('', 'YuVideoController@lists');
            Route::get('lists_ajax', 'YuVideoController@listsAjax');
            Route::get('add', 'YuVideoController@edit');
            Route::any('edit', 'YuVideoController@edit');
            Route::post('delete', 'YuVideoController@delete');
            Route::post('status', 'YuVideoController@status');
            Route::post('position', 'YuVideoController@position');

        });

        //小王子管理
        Route::prefix('prince')->group(function () {
            Route::get('', 'PrinceController@lists');
            Route::get('lists_ajax', 'PrinceController@listsAjax');
            Route::get('add', 'PrinceController@edit');
            Route::any('edit', 'PrinceController@edit');
            Route::post('delete', 'PrinceController@delete');
            Route::post('status', 'PrinceController@status');
            Route::post('position', 'PrinceController@position');

        });

        //医生说管理
        Route::prefix('doctor_say')->group(function () {
            Route::get('', 'DoctorSayController@lists');
            Route::get('lists_ajax', 'DoctorSayController@listsAjax');
            Route::get('add', 'DoctorSayController@edit');
            Route::any('edit', 'DoctorSayController@edit');
            Route::post('status', 'DoctorSayController@status');
            Route::post('delete', 'DoctorSayController@delete');
        });

        //日记管理
        Route::prefix('diary')->group(function () {
            Route::get('', 'DiaryController@lists');
            Route::get('lists_ajax', 'DiaryController@listsAjax');
            Route::post('status', 'DiaryController@status');
            Route::post('rem', 'DiaryController@rem');
            Route::post('delete', 'DiaryController@delete');
        });

        //资讯管理
        Route::prefix('info')->group(function () {
            Route::get('', 'InfoController@lists');
            Route::get('lists_ajax', 'InfoController@listsAjax');
            Route::get('add', 'InfoController@edit');
            Route::any('edit', 'InfoController@edit');
            Route::post('status', 'InfoController@status');
            Route::post('delete', 'InfoController@delete');
            Route::post('position','InfoController@position');
            Route::post('push', 'InfoController@push');
        });

        //朋友圈
        Route::prefix('friend')->group(function () {
            Route::get('', 'FriendController@lists');
            Route::get('lists_ajax', 'FriendController@listsAjax');
            Route::post('status', 'FriendController@status');
            Route::post('delete', 'FriendController@delete');
            Route::any('play_url', 'FriendController@play_url');
        });
    });

    /**
     ***************会员*******************
     */
    Route::group(['namespace' => 'Member'], function () {
        //会员组
        Route::prefix('member_group')->group(function () {
            Route::get('', 'GroupController@lists');
            Route::get('lists_ajax', 'GroupController@listsAjax');
            Route::get('add', 'GroupController@edit');
            Route::any('edit', 'GroupController@edit');
            Route::post('delete', 'GroupController@delete');

        });

        //会员
        Route::prefix('member')->group(function () {
            Route::get('', 'MemberController@lists');
            Route::get('lists_ajax', 'MemberController@listsAjax');
            Route::get('add', 'MemberController@edit');
            Route::any('edit', 'MemberController@edit');
            Route::post('delete', 'MemberController@delete');
            Route::post('status', 'MemberController@status');
            Route::post('talent', 'MemberController@talent');
            Route::post('live', 'MemberController@live');
            Route::post('live_msg', 'MemberController@live_msg');

            Route::get('simple_add', 'MemberController@simpleEdit');
            Route::any('simple_edit', 'MemberController@simpleEdit');
            Route::get('department', 'MemberController@department');
        });

        //部门管理
        Route::prefix('member_department')->group(function () {
            Route::get('', 'MemberDepartmentController@lists');
            Route::get('lists_ajax', 'MemberDepartmentController@listsAjax');
            Route::get('add', 'MemberDepartmentController@edit');
            Route::any('edit', 'MemberDepartmentController@edit');
            Route::post('delete', 'MemberDepartmentController@delete');
        });

        //账户明细列表
        Route::prefix('account')->group(function () {
            Route::get('', 'AccountController@lists');
            Route::get('lists_ajax', 'AccountController@listsAjax');
            Route::any('balance_detail', 'AccountController@balanceDetail');
            Route::any('meibi_detail', 'AccountController@meibiDetail');
            Route::any('point_detail', 'AccountController@pointDetail');
            Route::get('balance_detail_ajax', 'AccountController@balanceAjax');
            Route::get('meibi_detail_ajax', 'AccountController@meibiAjax');
            Route::get('point_detail_ajax', 'AccountController@pointAjax');
            Route::post('balance_add', 'AccountController@balanceAdd');//资金添加
            Route::post('meibi_add', 'AccountController@meibiAdd');//美币添加
            Route::post('point_add', 'AccountController@pointAdd');//积分添加
            Route::post('balance_reduce', 'AccountController@balanceReduce');//资金扣除
            Route::post('meibi_reduce', 'AccountController@meibiReduce');//美币扣除
            Route::post('point_reduce', 'AccountController@pointReduce');//积分扣除
        });


        //美币明细列表
        Route::prefix('meibi')->group(function () {
            Route::get('', 'MeibiController@lists');
            Route::get('lists_ajax', 'MeibiController@listsAjax');
        });

        //余额明细列表
        Route::prefix('balance')->group(function () {
            Route::get('', 'BalanceController@lists');
            Route::get('lists_ajax', 'BalanceController@listsAjax');
        });
        



        //提现管理
        Route::prefix('withdraw')->group(function () {
            Route::get('', 'WithdrawController@lists');
            Route::get('lists_ajax', 'WithdrawController@listsAjax');
            Route::get('add', 'WithdrawController@edit');
            Route::any('edit', 'WithdrawController@edit');
            Route::post('delete', 'WithdrawController@delete');
            Route::post('status', 'WithdrawController@status');
        });
    });

    /**
     ***************商品*******************
     */
    Route::group(['namespace' => 'Goods'], function () {
        //商品管理
        Route::prefix('goods')->group(function () {
            Route::get('', 'GoodsController@lists');
            Route::get('lists_ajax', 'GoodsController@listsAjax');
            Route::get('select_category', 'GoodsController@SelectCategory');
            Route::get('get_delivery', 'GoodsController@getDelivery');
            Route::get('add', 'GoodsController@edit');
            Route::any('edit', 'GoodsController@edit');
            Route::post('delete', 'GoodsController@delete');
            Route::post('status', 'GoodsController@status');
            Route::post('rem', 'GoodsController@rem');
            Route::post('position', 'GoodsController@position');
        });

        //商品分类
        Route::prefix('category')->group(function () {
            Route::get('', 'CategoryController@lists');
            Route::get('add', 'CategoryController@edit');
            Route::any('edit', 'CategoryController@edit');
            Route::post('delete', 'CategoryController@delete');
            Route::post('status', 'CategoryController@status');
        });

        //商品规格
        Route::prefix('spec')->group(function () {
            Route::get('', 'SpecController@lists');
            Route::get('lists_ajax', 'SpecController@listsAjax');
            Route::get('add', 'SpecController@edit');
            Route::any('edit', 'SpecController@edit');
            Route::post('delete', 'SpecController@delete');
            Route::post('position', 'SpecController@position');
        });
        //商品规格值
        Route::prefix('spec_value')->group(function () {
            Route::get('', 'SpecValueController@lists');
            Route::get('lists_ajax', 'SpecValueController@listsAjax');
            Route::get('add', 'SpecValueController@edit');
            Route::any('edit', 'SpecValueController@edit');
            Route::post('delete', 'SpecValueController@delete');
            Route::post('position', 'SpecValueController@position');
        });

        //商品属性
        Route::prefix('attribute')->group(function () {
            Route::get('', 'AttributeController@lists');
            Route::get('lists_ajax', 'AttributeController@listsAjax');
            Route::get('add', 'AttributeController@edit');
            Route::any('edit', 'AttributeController@edit');
            Route::post('delete', 'AttributeController@delete');
            Route::post('position', 'AttributeController@position');
        });
        //商品属性值
        Route::prefix('attribute_value')->group(function () {
            Route::get('', 'AttributeValueController@lists');
            Route::get('lists_ajax', 'AttributeValueController@listsAjax');
            Route::get('add', 'AttributeValueController@edit');
            Route::any('edit', 'AttributeValueController@edit');
            Route::post('delete', 'AttributeValueController@delete');
            Route::post('position', 'AttributeValueController@position');
        });

        //品牌
        Route::prefix('brand')->group(function () {
            Route::get('', 'BrandController@lists');
            Route::get('lists_ajax', 'BrandController@listsAjax');
            Route::get('add', 'BrandController@edit');
            Route::any('edit', 'BrandController@edit');
            Route::post('delete', 'BrandController@delete');
            Route::post('status', 'BrandController@status');
            Route::post('position', 'BrandController@position');
        });

        //配送方式
        Route::prefix('delivery')->group(function () {
            Route::get('', 'DeliveryController@lists');
            Route::get('lists_ajax', 'DeliveryController@listsAjax');
            Route::get('add', 'DeliveryController@edit');
            Route::any('edit', 'DeliveryController@edit');
            Route::post('delete', 'DeliveryController@delete');
            Route::post('status', 'DeliveryController@status');
        });



    });

    /**
     ***************优惠券*******************
     */
    Route::group(['namespace' => 'Coupons'], function () {
        //优惠券
        Route::prefix('coupons')->group(function () {
            Route::get('', 'CouponsController@lists');
            Route::get('lists_ajax', 'CouponsController@listsAjax');
            Route::get('add', 'CouponsController@edit');
            Route::any('edit', 'CouponsController@edit');
            Route::post('delete', 'CouponsController@delete');
            Route::post('status', 'CouponsController@status');

            Route::get('generate_lists', 'CouponsController@generateLists'); //查看优惠券
            Route::get('generate_ajax', 'CouponsController@generateAjax');
            Route::any('generate', 'CouponsController@generate');//生成优惠券

            Route::any('detail_set_user', 'CouponsController@detail_set_user');//绑定用户
            Route::post('is_close', 'CouponsController@is_close');
            Route::post('coupons_detail_delete', 'CouponsController@coupons_detail_delete');
        });
    });


    /**
     ***************订单*******************
     */
    Route::group(['namespace' => 'Order'], function () {
        //订单管理
        Route::prefix('order')->group(function () {
            Route::get('', 'OrderController@lists');
            Route::get('lists_ajax', 'OrderController@listsAjax');
            Route::any('detail', 'OrderController@detail');
            Route::any('delivery', 'OrderController@delivery');
            Route::post('cancel', 'OrderController@cancel');
            //Route::post('delivery', 'OrderController@delivery');
            Route::post('details', 'OrderController@details');
        });
    });

    /**
     ***************资讯*******************
     */
    Route::group(['namespace' => 'Information'], function () {

    });

    /**
     ***************会长&集团*******************
     */
    Route::group(['namespace' => 'President'], function () {
        //会长视频
        Route::prefix('yuvideo')->group(function () {
            Route::get('', 'YuVideoController@lists');
            Route::get('lists_ajax', 'YuVideoController@listsAjax');
            Route::get('add', 'YuVideoController@edit');
            Route::any('edit', 'YuVideoController@edit');
            Route::post('delete', 'YuVideoController@delete');
            Route::post('status', 'YuVideoController@status');
            Route::post('position', 'YuVideoController@position');
        });

        //会长相册
        Route::prefix('yualbum')->group(function () {
            Route::get('', 'YuAlbumController@lists');
            Route::get('lists_ajax', 'YuAlbumController@listsAjax');
            Route::get('add', 'YuAlbumController@edit');
            Route::any('edit', 'YuAlbumController@edit');
            Route::post('delete', 'YuAlbumController@delete');
            Route::post('status', 'YuAlbumController@status');
            Route::post('position', 'YuAlbumController@position');
        });

        //会长语录
        Route::prefix('yuquotations')->group(function () {
            Route::get('', 'YuQuotationsController@lists');
            Route::get('lists_ajax', 'YuQuotationsController@listsAjax');
            Route::get('add', 'YuQuotationsController@edit');
            Route::any('edit', 'YuQuotationsController@edit');
            Route::post('delete', 'YuQuotationsController@delete');
            Route::post('status', 'YuQuotationsController@status');
            Route::post('position', 'YuQuotationsController@position');
        });

        //会长著作
        Route::prefix('yubook')->group(function () {
            Route::get('', 'YuBookController@lists');
            Route::get('lists_ajax', 'YuBookController@listsAjax');
            Route::get('add', 'YuBookController@edit');
            Route::any('edit', 'YuBookController@edit');
            Route::post('delete', 'YuBookController@delete');
            Route::post('status', 'YuBookController@status');
            Route::post('position', 'YuBookController@position');
        });

        //会长互动
        Route::prefix('yumessage')->group(function () {
            Route::get('', 'YuMessageController@lists');
            Route::get('lists_ajax', 'YuMessageController@listsAjax');
            Route::get('add', 'YuMessageController@edit');
            Route::any('edit', 'YuMessageController@edit');
            Route::post('delete', 'YuMessageController@delete');
            Route::post('status', 'YuMessageController@status');
            Route::post('position', 'YuMessageController@position');
        });
    });


    Route::group(['namespace' => 'Group'], function () {
        //集团管理

        //诊所管理
        Route::prefix('clinic')->group(function () {
            Route::get('', 'ClinicController@lists');
            Route::get('lists_ajax', 'ClinicController@listsAjax');
            Route::get('add', 'ClinicController@edit');
            Route::any('edit', 'ClinicController@edit');
            Route::post('delete', 'ClinicController@delete');
            Route::post('status', 'ClinicController@status');
            Route::post('position', 'ClinicController@position');
        });

        //诊所城市
        Route::prefix('clinic_city')->group(function () {
            Route::get('', 'ClinicCityController@lists');
            Route::get('lists_ajax', 'ClinicCityController@listsAjax');
            Route::get('add', 'ClinicCityController@edit');
            Route::any('edit', 'ClinicCityController@edit');
            Route::post('delete', 'ClinicCityController@delete');
            Route::post('status', 'ClinicCityController@status');
            Route::post('position', 'ClinicCityController@position');
        });

        //医生管理
        Route::prefix('doctor')->group(function () {
            Route::get('lists', 'DoctorController@lists');
            Route::get('lists_ajax', 'DoctorController@listsAjax');
            Route::get('add', 'DoctorController@edit');
            Route::any('edit', 'DoctorController@edit');
            Route::post('delete', 'DoctorController@delete');
            Route::post('status', 'DoctorController@status');
            Route::post('position', 'DoctorController@position');
        });

        //医生案例管理
        Route::prefix('doctor_case')->group(function () {
            Route::get('lists', 'DoctorCaseController@lists');
            Route::get('lists_ajax', 'DoctorCaseController@listsAjax');
            Route::get('add', 'DoctorCaseController@edit');
            Route::any('edit', 'DoctorCaseController@edit');
            Route::post('delete', 'DoctorCaseController@delete');
            Route::post('status', 'DoctorCaseController@status');
            Route::post('position', 'DoctorCaseController@position');
        });

        //医生视频管理
        Route::prefix('doctor_video')->group(function () {
            Route::get('lists', 'DoctorVideoController@lists');
            Route::get('lists_ajax', 'DoctorVideoController@listsAjax');
            Route::get('add', 'DoctorVideoController@edit');
            Route::any('edit', 'DoctorVideoController@edit');
            Route::post('delete', 'DoctorVideoController@delete');
            Route::post('status', 'DoctorVideoController@status');
            Route::post('position', 'DoctorVideoController@position');
        });


    });


    /**
     ***************H5*******************
     */
    Route::group(['namespace' => 'H5'], function () {
        //H5-星座占卜
        Route::prefix('augury')->group(function () {
            Route::get('', 'AuguryController@lists');
            Route::get('lists_ajax', 'AuguryController@listsAjax');
            Route::get('add', 'AuguryController@edit');
            Route::any('edit', 'AuguryController@edit');
            Route::post('delete', 'AuguryController@delete');
        });

        //H5-签到管理
        Route::prefix('sign')->group(function () {
            //服务商
            Route::get('service_lists', 'SignController@service_lists');
            Route::get('service_lists_ajax', 'SignController@service_listsAjax');
            Route::post('service_shoper_num', 'SignController@service_shoper_num');
            Route::post('service_delete', 'SignController@service_delete');
            Route::post('service_status', 'SignController@service_status');
            Route::post('service_group', 'SignController@service_group');

            //店家
            Route::get('shop_owner_lists', 'SignController@shop_owner_lists');
            Route::get('shop_owner_lists_ajax', 'SignController@shop_owner_listsAjax');
            Route::get('shop_owner_add', 'SignController@shop_owner_edit');
            Route::any('shop_owner_edit', 'SignController@shop_owner_edit');
            Route::post('shop_owner_delete', 'SignController@shop_owner_delete');
            Route::post('shop_owner_status', 'SignController@shop_owner_status');

            //陪同
            Route::get('accompany_lists', 'SignController@accompany_lists');
            Route::get('accompany_lists_ajax', 'SignController@accompany_listsAjax');
            Route::get('accompany_add', 'SignController@accompany_edit');
            Route::any('accompany_edit', 'SignController@accompany_edit');
            Route::post('accompany_delete', 'SignController@accompany_delete');
            Route::post('accompany_status', 'SignController@accompany_status');


            Route::post('shoper_group', 'SignController@shoper_group');


            //中奖
            Route::get('lottery_lists', 'SignController@lottery_lists');
            Route::get('lottery_lists_ajax', 'SignController@lottery_listsAjax');
            Route::get('lottery_add', 'SignController@lottery_edit');
            Route::any('lottery_edit', 'SignController@lottery_edit');
            Route::post('lottery_delete', 'SignController@lottery_delete');
            Route::post('lottery_status', 'SignController@lottery_status');
        });

    });
    
    /**
     ***************笔记管理*******************
     */
    Route::group(['namespace' => 'Note'], function () {
        //优惠券
        Route::prefix('note')->group(function () {
            Route::get('', 'NoteController@lists');
            Route::get('lists_ajax', 'NoteController@listsAjax');
            Route::get('add', 'NoteController@edit');
            Route::any('edit', 'NoteController@edit');
            Route::post('delete', 'NoteController@delete');
            Route::post('status', 'NoteController@status');
        });
    });


});