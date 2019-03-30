<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * 图集
 * Class Article
 * @package App\Models
 */
class YuMessage extends Model
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '已处理',
        self::STATUS_OFF => '待处理'
    ];

    protected $table = 'yu_message';
    protected $guarded = ['id'];


}