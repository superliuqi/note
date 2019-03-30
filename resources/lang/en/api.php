<?php

    return [
        //公共错误提示
        'invalid_request'            => '10000|无效的请求',
        'missing_params'             => '10001|缺少参数',
        'invalid_params'             => '10002|无效的参数',
        'invalid_session'            => '10003|用户未登录',
        'invalid_sign'               => '10004|无效的签名',
        'invalid_token'              => '10005|无效的token',
        'invalid_device'             => '10006|无效的设备',
        'timestamp_error'            => '10007|无效的时间戳',
        'timestamp_out'              => '10008|时间超时',
        'fail'                       => '10009|操作失败',
        'content_is_empty'           => '10010|内容为空',
        'mobile_error'               => '10011|手机号码格式错误',
        'sms_captcha_error'          => '10012|手机验证码错误',
        'sms_captcha_time_out'       => '10013|手机验证已经过期',
        'sms_frequent'               => '10014|短信发送太频繁',
        'sms_send_fail'              => '10015|短信发送失败',
        'role_error'                 => '10016|权限不足',

        //用户
        'member_is_login'            => '20001|用户已经登录',
        'member_password_error'      => '20002|用户名或密码错误',
        'member_old_password_error'  => '20003|用户原密码错误',
        'member_pay_password_notset' => '20004|支付密码未设置',
        'member_pay_password_isset'  => '20005|已经设置过支付密码',
        'member_pay_password_error'  => '20006|支付密码错误',
        'member_is_focus'            => '20007|已经关注',
        'member_is_repeat'           => '20008|用户已经存在',
        'member_no_login'            => '20009|用户未登录',
        'not_del_other_comment'      => '20010|不能删除他人评论',
        'member_video_score_limit'   => '20011|每天观看视频可获取的积分已上限',
        'member_live_score_limit'    => '20012|每天观看直播可获取的积分已上限',

        //直播
        'live_no_permissions'        => '30001|没有直播权限',
        'live_create_room_fail'      => '30002|直播聊天室创建失败',
        'live_no_play'               => '30003|没有直播',
        'live_watch_role_error'      => '30004|没有观看权限',
        'live_is_end'                => '30005|直播已经结束',
        'live_gift_error'            => '30006|礼物信息错误',
        'live_manager_exist'         => '30007|管理员已存在',
        'live_manager_toplimit'      => '30008|管理员上限',
        'live_not_share'             => '30009|直播有观看权限无法分享',
        'live_password_error'        => '30010|直播间密码错误',
        'live_password_type_error'   => '30011|密码格式错误',
        //红包
        'red_number_error'           => '30050|红包个数不能超过红包金额的10倍',
        'red_amount_max'             => '30051|红包金额超过最大限制',
        'red_number_max'             => '30052|红包个数超过最大限制',
        'red_repeat'                 => '30053|已经抢过了',
        'red_is_over'                => '30054|手慢了，红包抢完了',
        'red_meibi_not_meet'         => '30055|账户余额不足',
        'red_not_start'              => '30056|红包还没开始，请稍后再试',
        //连麦
        'micro_is_requested'         => '30100|您已经申请过连麦',
        'micro_is_end'               => '30101|连麦已结束',
        'micro_liver_cant_request'   => '30102|主播不能发起连麦',
        'micro_is_linked'            => '30103|主播正在连麦中',
        'micro_link_is_limit'        => '30104|连麦申请人数上限，请稍后再试',

        //资金
        'balance_no_enough'          => '40001|账户余额不足',
        'balance_event_error'        => '40002|类型错误',

        //支付
        'pay_result_error'           => '50000|',//第三方返回错误
        'pay_platform_error'         => '50001|没有合适的支付方式',
        'pay_type_error'             => '50002|支付方式不适用该类型订单',
        'pay_payment_error'          => '50003|支付方式不存在',
        'pay_data_error'             => '50004|支付信息错误',
        'pay_sign_error'             => '50005|签名验证失败',
        'pay_openid_error'           => '50006|用户openid错误',
        'pay_recharge_ispay'         => '50007|充值订单已经支付',
        'pay_amount_error'           => '50008|支付金额错误',
        'pay_add_trade_error'        => '50009|交易单创建失败',

        //订单
        'order_goods_error'          => '60001|商品不存在或未上架',
        'order_goods_stock_error'    => '60002|库存不足',
        'order_address_error'        => '60003|收货地址不存在',
        'order_delivery_error'       => '60004|配送方式不存在',
        'order_no_delivery'          => '60005|存在不可配送的商品',
        'order_coupons_error'        => '60006|优惠券不存在',
        'order_add_fail'             => '60007|订单添加失败',
        'order_status_error'         => '60008|订单状态错误',
        'order_trade_error'          => '60009|交易单不存在',
        'order_not_confirm'          => '60010|该订单无法确认收货',
        'order_not_refund'           => '60011|该订单无法进行退款',
        'order_refund_fail'          => '60012|退款操作失败',
        'order_not_cancel'           => '60013|该订单无法取消',
        'order_comment_fail'         => '60014|评价失败',
        'order_goods_less'           => '60015|购买商品数量不能小于最少起订数量',
        'comment_fail'               => '60016|评价失败',
        'comment_info_error'         => '60017|评论信息不存在',

        //签到
        'qiandao_is_repeat'          => '70001|今日已经签到',
        'video_score_limit'          => '70002|今日获取观看视频积分上限',
        'live_score_limit'           => '70003|今日获取观看直播积分上限',

        //日记 虞圈
        'diary_no_exist'             => '80001|日记不存在',
        'friend_img_limit'           => '80002|图片张数超过限制',
        'friend_is_praise'           => '80003|该条朋友圈已点过赞',
        'friend_comment_error'       => '80004|不能回复自己的评论',
        'friend_video_limit'         => '80005|视频个数超过限制',
        'friend_delete_error'        => '80006|只能删除自己的状态',
    
    
        //优惠券
        'coupon_no_exist'            => '90001|优惠券不存在',
        'coupon_exchange_less'       => '90002|兑换优惠券数量必须大于0',
        'coupon_no_use'              => '90003|所选优惠券已禁用或已使用',
        'coupon_is_over'             => '90004|优惠券已过期',

        //积分
        'point_less'                 => '11001|账户积分不足',
    ];
