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
 * 优惠券
 * Class Payment
 * @package App\Models
 */
class Coupons extends Model
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '锁定'
    ];

    protected $table = 'coupons';
    protected $guarded = ['id'];

}