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
 * 订单日志
 * Class Payment
 * @package App\Models
 */
class OrderLog extends Model
{
    protected $table = 'order_log';
    protected $guarded = ['id'];
}