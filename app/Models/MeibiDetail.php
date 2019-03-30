<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeibiDetail extends Model
{
    //类型
    const TYPE_INCR = 1;//增加
    const TYPE_RECR = 2;//减少

    const EVENT_SYSTEM_RECHARGE = 1;//系统充值
    const EVENT_SYSTEM_DEDUCT = 2;//系统扣除
    const EVENT_RECHARGE = 3;//充值
    const EVENT_WITHDRAW = 4;//兑换
    const EVENT_GIFT_INCR = 5;//礼物收入
    const EVENT_GIFT_BUY = 6;//礼物购买
    const EVENT_RED_SEND = 7;//发红包
    const EVENT_RED_RECEIVE = 8;//抢红包
    const EVENT_RED_TIME_OUT = 9;//红包超时退回

    const EVENT_DESC = [
        self::EVENT_SYSTEM_RECHARGE => '系统充值',
        self::EVENT_SYSTEM_DEDUCT => '系统扣除',
        self::EVENT_RECHARGE => '充值',
        self::EVENT_WITHDRAW => '兑换',
        self::EVENT_GIFT_INCR => '礼物收入',
        self::EVENT_GIFT_BUY => '礼物购买',
        self::EVENT_RED_SEND => '发红包',
        self::EVENT_RED_RECEIVE => '抢红包',
        self::EVENT_RED_TIME_OUT => '红包超时退回',
    ];

    protected $table = 'meibi_detail';
    protected $guarded = ['id'];
}
