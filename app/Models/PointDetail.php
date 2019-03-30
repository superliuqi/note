<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointDetail extends Model
{
    //类型
    const TYPE_INCR = 1;//增加
    const TYPE_RECR = 2;//减少

    const EVENT_SYSTEM_RECHARGE = 1;//系统充值
    const EVENT_SYSTEM_DEDUCT = 2;//系统扣除
    const EVENT_SYSTEM_REWARD = 3;//系统奖励
    const EVENT_ORDER_PAY = 4;//订单支付
    const EVENT_ORDER_REFUND = 5;//订单退款
    const EVENT_COUPONS_EXCHANGE = 6;//兑换优惠券
    const EVENT_GOODS_EXCHANGE = 7;//兑换商品

    const EVENT_DESC = [
        self::EVENT_SYSTEM_RECHARGE => '系统充值',
        self::EVENT_SYSTEM_DEDUCT => '系统扣除',
        self::EVENT_SYSTEM_REWARD => '系统奖励',
        self::EVENT_ORDER_PAY => '订单支付',
        self::EVENT_ORDER_REFUND => '订单退款',
        self::EVENT_COUPONS_EXCHANGE => '兑换优惠券',
        self::EVENT_GOODS_EXCHANGE => '兑换商品',
    ];
    protected $table = 'point_detail';
    protected $guarded = ['id'];
}
