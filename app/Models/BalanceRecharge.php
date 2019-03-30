<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceRecharge extends Model
{
    protected $table = 'balance_recharge';
    protected $guarded = ['id'];

    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_OFF => '未支付',
        self::STATUS_ON => '已支付'
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
    public static function getRechargeNo()
    {
        $recharge_no = 'B' . date('YmdHis', time()) . str_random(6);
        $exists = self::where('recharge_no',$recharge_no)->first();
        if($exists){
            return self::getRechargeNo();
        }else{
            return $recharge_no;
        }
    }
}
