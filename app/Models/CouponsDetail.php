<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponsDetail extends Model
{
    protected $table = 'coupons_detail';
    protected $guarded = ['id'];

    //状态
    const USED_OFF = 0;
    const USED_ON = 1;

    //优惠类型
    const CLOSED_OFF = 0;
    const CLOSED_ON = 1;

    //优惠券来源
    const FROM_ADMIN = 0;
    const FROM_POINT = 1;

    const STATUS_DESC = [
        self::USED_OFF => '未使用',
        self::USED_ON => '已使用'
    ];
    const TYPE_DESC = [
        self::CLOSED_OFF => '未禁用',
        self::CLOSED_ON => '已禁用'
    ];
    const FROM_DESC = [
        self::FROM_ADMIN => '后台赠送',
        self::FROM_POINT => '积分兑换'
    ];
    const UPDATED_AT='updated_at';
    const CREATED_AT = 'created_at';

    /**
     * 返还优惠券
     */
    public static function backCoupons($coupons_id = 0)
    {
        if ($coupons_id) {
            self::where('id', $coupons_id)->update(['status' => 0]);
        }
    }
}
