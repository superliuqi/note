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
 * 广告组
 * Class AdvGroup
 * @package App\Models
 */
class AdvGroup extends Model
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '待审'
    ];

    protected $table = 'adv_group';
    protected $guarded = ['id'];

}