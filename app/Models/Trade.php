<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $table = 'trade';
    protected $guarded = ['id'];

    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_OFF => '未支付',
        self::STATUS_ON => '已支付'
    ];

    //类型
    const TYPE_ORDER = 1;
    const TYPE_BALANCE = 2;
    const TYPE_MEIBI = 3;

    const TYPE_DESC = [
        self::TYPE_ORDER => '订单',
        self::TYPE_BALANCE => '钱包',
        self::TYPE_MEIBI => '美币'
    ];

    //风险订单提示
    const FLAG_NO = 0;
    const FLAG_YES = 1;

    const FLAG_DESC = [
        self::FLAG_NO => '正常',
        self::FLAG_YES => '风险'
    ];

    /**
     * 生成交易单号
     * @return string
     */
    public static function getTradeNo() {
        $order_no = 'T' . date('YmdHis', time()) . str_random(6);
        return $order_no;
    }
}
