<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/11
 * Time: 下午4:46
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 短信发送记录
 * Class Adv
 * @package App\Models
 */
class SmsLog extends Model
{
    protected $table = 'sms_log';
    protected $guarded = ['id'];

}
