<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 订单
 * Class Payment
 * @package App\Models
 */
class Refund extends Model
{
    //状态
    const STATUS_NO_REFUND = 0;
    const STATUS_APPLY_REFUND = 1;
    const STATUS_START_REFUND = 2;
    const STATUS_FINISH_REFUND = 3;
    const STATUS_CLOSE_REFUND = 4;

    const STATUS_DESC = [
        self::STATUS_NO_REFUND => '没有售后',
        self::STATUS_APPLY_REFUND => '申请售后',
        self::STATUS_START_REFUND => '售后中',
        self::STATUS_FINISH_REFUND => '售后完成',
        self::STATUS_CLOSE_REFUND => '售后关闭',
    ];

    const REFUND_STATUS_DESC = [
        self::STATUS_NO_REFUND => '',
        self::STATUS_APPLY_REFUND => '退款中',
        self::STATUS_START_REFUND => '退款中',
        self::STATUS_FINISH_REFUND => '已退款',
        self::STATUS_CLOSE_REFUND => '取消退款',
    ];
    protected $table = 'refund';
    protected $guarded = ['id'];
    /**
     * 获取状态
     * @param string $status
     * @return mixed|string
     */
    public static function getRefundStatus($status = '')
    {
        $status_desc = self::STATUS_DESC;
        return array_key_exists($status, $status_desc) ? $status_desc[$status] : '';
    }
}