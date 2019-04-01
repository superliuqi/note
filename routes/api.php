<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('v1')->namespace('Api\V1')->group(function () {
    //系统
    Route::prefix('config')->group(function () {
        Route::get('api_config','ConfigController@apiConfig');
    });
    //首页
    Route::prefix('index')->group(function () {
        Route::get('/', 'IndexController@index'); //首页
        Route::get('yimei_video/{page?}/{pagesize?}', 'IndexController@yimeiSayVideo'); //医美说视频
        Route::get('core_list/{page?}/{pagesize?}', 'IndexController@coreProjectList'); //核心项目列表
        Route::get('prince/{page?}/{pagesize?}', 'IndexController@prince'); //核心项目列表
    });
    Route::get('live/{page?}/{pagesize?}', 'LiveController@index');//直播列表
    Route::get('download', 'IndexController@downloadApp');//下载地址
    Route::get('group_index', 'GroupController@index'); //集团首页
    Route::get('area/{parent_id?}', 'AreaController@index');//获取地区
    Route::get('project', 'GroupController@project'); //项目首页
    //资讯
    Route::prefix('info')->group(function () {
        Route::get('list/{page?}/{pagesize?}', 'IndexController@infoList'); //集团资讯列表
        Route::get('comment/{info_id}/{page?}/{pagesize?}', 'IndexController@infoComment'); //资讯评价列表
    });
    //于文红
    Route::prefix('yu')->group(function () {
        Route::get('index', 'GroupController@yuWenhong'); //于文红-首页
        Route::get('video/{page?}/{pagesize?}', 'GroupController@video'); //于文红-视频
        Route::get('album/{page?}/{pagesize?}', 'GroupController@album'); //于文红-相册
        Route::get('album/detail/{id}/{page?}/{pagesize?}', 'GroupController@albumDetail')->where('id', '[0-9]+');
        Route::get('books/{page?}/{pagesize?}', 'GroupController@books'); //于文红-著作
//        Route::get('book/detail/{book_id}', 'GroupController@bookDetail')->where('book_id', '[0-9]+'); //于文红-著作详情
        Route::get('talk', 'GroupController@talkLog'); //于文红-语录
        Route::get('talk/detail/{talk_id}', 'GroupController@talkLogDetail')->where('talk_id', '[0-9]+');//于文红-语录详情
        Route::post('message', 'GroupController@saveMessage'); //于文红-互动
    });
    //诊所
    Route::prefix('clinic')->group(function () {
        Route::get('list', 'GroupController@clinic');  //诊所列表
        Route::get('detail/{id}', 'GroupController@clinicDetail')->where('id', '[0-9]+'); //诊所详情
    });
    //医生或设计师
    Route::prefix('doctor')->group(function () {
        Route::get('list/{type}', 'GroupController@doctor')->where('type', '[0-9]+'); //医生或设计师
        Route::get('detail/{id}', 'GroupController@doctorDetail')->where('id', '[0-9]+'); //医生或设计师详情
    });
    //社区日记
    Route::prefix('diary')->group(function () {
        Route::get('list', 'CommunityController@diaryList'); //日记列表
        Route::get('detail/{diary_id}', 'CommunityController@diaryDetail'); //日记详情
        Route::get('comment/{diary_id}/{page?}/{pagesize?}', 'CommunityController@diaryComment'); //日记评价列表
    });

    //社区
    Route::prefix('community')->group(function () {
        Route::get('index', 'CommunityController@index'); //社区首页
        Route::get('doctor_say/{page?}/{pagesize?}', 'CommunityController@doctorSay'); //医生说
        Route::get('get_tags/{type}', 'CommunityController@getTags')->where('type', '[0-9]+'); //获取指定类型标签列表
        Route::get('hot_item/{page?}/{pagesize?}', 'CommunityController@hotItem'); //最热项目
        Route::get('medicine/{page?}/{pagesize?}', 'CommunityController@medicine'); //医美科普
    });

    //商城
    Route::prefix('goods')->group(function () {
        Route::get('/', 'GoodsController@index');//商城首页
        Route::get('search', 'GoodsController@search');//商品列表
        Route::get('detail/{id}', 'GoodsController@detail')->where('id', '[0-9]+');//商品详情
        Route::get('comment_list/{id}/{page?}/{pagesize?}', 'GoodsController@comment_list')->where('id', '[0-9]+');//商品评价列表
    });

    Route::post('captcha', 'HelperController@captcha');//发送验证码

    //腾讯接口
    Route::prefix('tencent')->group(function () {
        Route::post('live', 'TencentController@live');//腾讯直播回调
    });

    //百度接口
    Route::prefix('baidu')->group(function () {
        Route::get('down_ios', 'BaiduController@downIos');
        Route::get('down_android', 'BaiduController@downAndroid');
    });

    //用户
    Route::post('register', 'LoginController@register')
        ->middleware(\App\Http\Middleware\CheckSmsCaptcha::class);//注册
    Route::post('login', 'LoginController@login');//账号密码登录
    Route::post('speed_login', 'LoginController@speedLogin')
        ->middleware(\App\Http\Middleware\CheckSmsCaptcha::class);//验证码登录
    Route::post('find_password', 'LoginController@findPassword')
        ->middleware(\App\Http\Middleware\CheckSmsCaptcha::class);//找回密码

    //支付相关
    Route::prefix('pay')->group(function () {
        Route::post('notify/{payment_id}', 'PayController@notify')->where('payment_id', '[0-9]+');//支付第三方回调
        Route::post('payment', 'PayController@payment');//支付方式列表
    });

    Route::post('aliyun_sts', 'HelperController@aliyunSts');//获取图片上传token
    Route::post('aliyun_token', 'HelperController@aliyunToken');//获取web图片上传token

    //h5 获取直播观看信息
    Route::prefix('live')->group(function () {
        Route::post('watch', 'LiveController@watch');//h5获取直播观看信息
    });

    //回播
    Route::prefix('replay')->group(function () {
        Route::get('list/{page?}/{pagesize?}', 'ReplayController@lists');//回播列表
    });

    //星座占卜
    Route::prefix('h5')->namespace('H5')->group(function(){
        Route::prefix('augury')->group(function(){
            Route::get('start','AuguryController@start');
            Route::get('res','AuguryController@result');
            Route::get('get','AuguryController@adminResult');
            Route::get('modify','AuguryController@modify');
            Route::get('option','AuguryController@option');
        });
    });



    Route::group(['middleware' => App\Http\Middleware\CheckSmsCaptcha::class], function () {
        Route::prefix('h5/signed')->namespace('H5')->group(function () {
            Route::post('check', 'SignedController@check');
        });
    });

    Route::prefix('h5')->namespace('H5')->group(function(){
        Route::prefix('signed')->group(function(){
            Route::post('captcha', 'SignedController@captcha');//验证码
            Route::post('wx/login', 'SignedController@wxLogin');//微信授权
            Route::post('sign', 'SignedController@sign');//签到
            Route::post('sign/info', 'SignedController@signInfo');//获取签到信息
            Route::post('lucky', 'SignedController@lucky');//抽奖
            Route::get('chief', 'SignedController@chief');//课长列表
            Route::get('identity', 'SignedController@identity');//陪同身份列表
            Route::get('list', 'SignedController@signList');//签到列表
            Route::get('lucky/num', 'SignedController@luckyDrawNum');//参与抽奖人数
        });
    });

    Route::group(['middleware' => App\Http\Middleware\CheckOpenid::class, 'prefix' => 'h5/signed','namespace'=>'H5'], function () {
        Route::post('shoper/info', 'SignedController@shoperInfo');//店家或陪同信息
        Route::post('shoper/add', 'SignedController@add');//店家或陪同增加
        Route::post('shoper/edit', 'SignedController@edit');//店家或陪同编辑
    });
    
    //虞圈
    Route::prefix('friend')->group(function () {
        Route::get('list', 'FriendController@list');//列表
        Route::get('detail', 'FriendController@detail');//详情
        Route::get('comment', 'FriendController@comment');//评论分页
    });


    //需要用户登录的
    Route::group(['middleware' => App\Http\Middleware\CheckSignToken::class], function () {

        //直播
        Route::prefix('live')->group(function () {
            Route::post('push_url', 'LiveController@pushUrl');//获取推流地址
            Route::post('start_push', 'LiveController@startPush');//开始推流
            Route::post('play', 'LiveController@play');//进入直播
            Route::post('leave', 'LiveController@leave');//退出直播间
            Route::post('close', 'LiveController@close');//关闭直播
            Route::post('room_user', 'LiveController@roomUser');//直播间用户列表
            Route::post('gift', 'LiveController@gift');//礼物列表
            Route::post('gift_buy', 'LiveController@giftBuy');//礼物购买
            Route::post('set_manager', 'LiveController@setManager');//设置管理员
            Route::post('del_manager', 'LiveController@delManager');//删除管理员
            Route::post('manager_list', 'LiveController@managerList');//管理员列表
            Route::post('check_manager', 'LiveController@checkManager');//查询是否管理员
            Route::post('set_shut_up', 'LiveController@setShutUp');//管理员禁言
            Route::post('del_shut_up', 'LiveController@delShutUp');//管理员解除禁言
            Route::post('gift/rank', 'LiveController@giftRank');//礼物榜单

        });

        //连麦
        Route::prefix('micro')->group(function () {
            Route::post('list','MicrophoneController@linkList');//连麦申请列表
            Route::post('link','MicrophoneController@linkMicro');//发起连麦请求
            Route::post('handle','MicrophoneController@handle');//主播同意/拒绝连麦
            Route::post('close','MicrophoneController@close');//连麦结束
            Route::post('play','MicrophoneController@getLinkPlayUrl');//连麦拉流地址
            Route::post('mix','MicrophoneController@liveMix');//通知云端混流
        });

        //回播
        Route::prefix('replay')->group(function () {
            Route::get('detail/{id?}/{password?}', 'ReplayController@detail');//回播详情
        });

        //订单
        Route::prefix('order')->group(function () {
            Route::post('list', 'OrderController@index'); //我的订单列表
            Route::post('detail', 'OrderController@orderDetail'); //订单详情
            Route::post('finished', 'OrderController@finished'); //订单完成
            Route::post('cancel', 'OrderController@cancel'); //订单取消
            Route::post('apply_refund', 'OrderController@applyRefund'); //订单取消
            Route::post('confirm', 'OrderController@confirm');//确认订单
            Route::post('confirm_price', 'OrderController@confirmPrice');//确认订单价格计算
            Route::post('submit', 'OrderController@submit');//提交订单
            Route::post('put_comment', 'OrderController@commentSave');//商品评价
        });
        //资讯相关
        Route::prefix('info')->group(function () {
            Route::post('is_like', 'IndexController@infoLike'); //资讯点赞
            Route::post('comment/put', 'IndexController@putInfoComment'); //发布资讯评价
            Route::post('comment/del', 'IndexController@delMyInfoComment');//删除我的资讯评价
        });

        //日记相关
        Route::prefix('diary')->group(function () {
            //美丽日记
            Route::post('add', 'MemberController@addDiary'); //写日记
            Route::post('delete', 'MemberController@delDiary'); //删除日记
            Route::post('my_list', 'CommunityController@myDiaryList'); //我的美丽日记列表
//            Route::post('attention_list', 'CommunityController@attentionDiary');//关注日记
            Route::post('is_like', 'CommunityController@isLike'); //日记点赞
            Route::post('comment/put', 'CommunityController@putComment'); //发布日记评价
        });

        Route::prefix('pay')->group(function () {
            Route::post('get_params', 'PayController@getParams');//请求支付参数
            Route::post('get_trade_status', 'PayController@getTradeStatus');//查询交易单是否支付成功
        });

        //会员个人信息
        Route::prefix('member')->group(function () {
            Route::post('/', 'MemberController@index');//个人中心首页
            Route::post('info', 'MemberController@info');//获取资料
            Route::post('info_fromid', 'MemberController@infoFromid');//获取指定用户的资料
            Route::post('edit_profile', 'MemberController@editProfile');//修改资料
            Route::post('up_password', 'MemberController@upPassword');
            Route::post('set_pay_password', 'MemberController@setPayPassword');
            Route::post('up_pay_password', 'MemberController@upPayPassword');
            Route::post('favorite', 'MemberController@favorite');//我的收藏
            Route::post('add_favorite', 'MemberController@addFavorite');//添加收藏
            Route::post('my_comment', 'MemberController@myComment');//我的评论
            Route::post('del_my_comment', 'MemberController@delMyComment');//我的评论
            Route::post('fans', 'MemberController@fans');
            Route::post('focus', 'MemberController@focus');
            Route::post('add_focus', 'MemberController@addFocus');
            Route::post('cancel_focus', 'MemberController@cancelFocus');
            Route::post('report', 'MemberController@report');
            Route::post('message', 'MemberController@message');
            Route::post('im_usersig', 'MemberController@imUserSig');
            //签到
            Route::post('qiandao','MemberController@qiandao');
            //观看直播或者视频 增加积分
            Route::post('add_point','MemberController@addPoint');
            Route::post('point','MemberController@userPoint'); //会员积分
            Route::post('search','MemberController@search'); //人员查询
            Route::post('select','MemberController@select'); //输入文字进行模糊搜索
        });

        //余额
        Route::prefix('balance')->group(function () {
            Route::post('recharge','BalanceController@recharge');//余额充值 获取单号
            Route::post('lists','BalanceController@lists');//余额明细
            Route::post('cash_rule','BalanceController@cashRule');//提现规则
            Route::post('withdraw','BalanceController@withdraw');//提现
        });

        //美币
        Route::prefix('meibi')->group(function () {
            Route::post('recharge','MeibiController@recharge'); //美币充值，获取单号
            Route::post('price_list','MeibiController@priceList'); //美币充值的价格列表
            Route::post('lists','MeibiController@lists');//美币明细
            Route::post('exchange','MeibiController@exchange');//美币兑换人民币
            Route::post('exchange_rule','MeibiController@exchangeRule');//美币兑换规则
            Route::post('get_balance','MeibiController@getBalance');//获取美币总额
        });

        //红包
        Route::prefix('red')->group(function () {
            Route::post('receive_list','RedController@receiveList');//收到的红包列表
            Route::post('send_list','RedController@sendList');//发出的红包列表
            Route::post('receive_detail','RedController@receiveDetail');//收到的单个红包详情
            Route::post('send_detail','RedController@sendDetail');//发出的单个红包详情
            Route::post('send_red', 'RedController@sendRed');//发红包
            Route::post('receive_red', 'RedController@receiveRed');//抢红包
            Route::post('delay/list','RedController@delayRedList');//延迟红包列表
        });

        //优惠券相关
        Route::prefix('coupons')->group(function () {
            Route::post('list', 'CouponsController@index');//可用优惠券列表
            Route::post('my_list', 'CouponsController@myList');//我的优惠券
        });

        //积分相关
        Route::prefix('point')->group(function () {
            Route::post('index','PointController@index'); //会员积分首页
            Route::post('detail/{page?}/{pagesize?}','PointController@detail'); //会员积分明细
            Route::post('ex_coupons','PointController@exchangeCoupons'); //积分兑换优惠券
            Route::post('exchange_log/{page?}/{pagesize?}','PointController@exchangeLog'); //兑换记录
        });
        
        //虞圈
        Route::prefix('friend')->group(function () {
            Route::post('add', 'FriendController@add');//发表状态
            Route::post('comment/add', 'FriendController@addComment');//添加评论
            Route::post('delete', 'FriendController@delete');//删除虞圈状态
            Route::post('praise', 'FriendController@praise');//点赞或者取消点赞
            Route::get('center', 'FriendController@myFriendList');//个人中心
        });
    });
});


