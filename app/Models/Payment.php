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
 * 支付方式
 * Class Payment
 * @package App\Models
 */
class Payment extends Model
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '待审'
    ];

    //类型
    const TYPE_LINE = 1;
    const TYPE_OFFLINE = 2;
    const TYPE_DESC = [
        self::TYPE_LINE => '线上',
        self::TYPE_OFFLINE => '线下',
    ];

    //使用客户端
    const CLIENT_TYPE_ALL = 1;
    const CLIENT_TYPE_WEB = 2;
    const CLIENT_TYPE_MOBILE = 3;
    const CLIENT_TYPE_APP = 4;

    const CLIENT_TYPE_DESC = [
        self::CLIENT_TYPE_ALL => '通用',
        self::CLIENT_TYPE_WEB => 'PC端',
        self::CLIENT_TYPE_MOBILE => '手机端',
        self::CLIENT_TYPE_APP => 'APP'
    ];

    //支付方式（同步数据库）
    const PAYMENT_WECHAT = 2;
    const PAYMENT_ALIPAY = 3;
    const PAYMENT_UNIONPAY = 4;
    const PAYMENT_BALANCE = 5;
    const PAYMENT_POINT = 6;
    const PAYMENT_DESC = [
        self::PAYMENT_WECHAT => '微信',
        self::PAYMENT_ALIPAY => '支付宝',
        self::PAYMENT_UNIONPAY => '银联',
        self::PAYMENT_BALANCE => '余额',
        self::PAYMENT_POINT => '积分'
    ];

    protected $table = 'payment';
    protected $guarded = ['id'];

}