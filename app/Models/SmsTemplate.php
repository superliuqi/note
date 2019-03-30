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
 * 短信模板
 * Class Adv
 * @package App\Models
 */
class SmsTemplate extends Model
{
    protected $table = 'sms_template';
    protected $guarded = ['id'];

}
