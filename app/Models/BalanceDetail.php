<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceDetail extends Model
{
    //类型
    const TYPE_INCR = 1;//增加
    const TYPE_RECR = 2;//减少

    const EVENT_SYSTEM_RECHARGE = 1;//系统充值
    const EVENT_SYSTEM_DEDUCT = 2;//系统扣除
    const EVENT_RECHARGE = 3;//充值
    const EVENT_WITHDRAW = 4;//提现
    const EVENT_WITHDRAW_REFUND = 5;//提现退款
    const EVENT_ORDER_PAY = 6;//订单支付
    const EVENT_ORDER_REFUND = 7;//订单退款
    const EVENT_MEIBI_EXCHANGE = 8;//美币兑换
    const EVENT_MEIBI_BUY = 9;//美币购买

    const EVENT_DESC = [
        self::EVENT_SYSTEM_RECHARGE => '系统充值',
        self::EVENT_SYSTEM_DEDUCT => '系统扣除',
        self::EVENT_RECHARGE => '充值',
        self::EVENT_WITHDRAW => '提现',
        self::EVENT_WITHDRAW_REFUND => '提现退款',
        self::EVENT_ORDER_PAY => '订单支付',
        self::EVENT_ORDER_REFUND => '订单退款',
        self::EVENT_MEIBI_EXCHANGE => '美币兑换',
        self::EVENT_MEIBI_BUY => '美币购买',
    ];

    protected $table = 'balance_detail';
    protected $guarded = ['id'];

}
