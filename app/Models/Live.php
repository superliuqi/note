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
 * 直播间
 * Class Article
 * @package App\Models
 */
class Live extends Model
{
    const STATUS_ON = 1;
    const STATUS_OFF = 0;

    const STATUS_DESC = [
        self::STATUS_OFF => '断开',
        self::STATUS_ON => '直播中'
    ];

    const IS_REM_ON = 1;
    const IS_REM_OFF = 0;

    const IS_REM_DESC = [
        self::IS_REM_OFF => '不推荐',
        self::IS_REM_ON => '推荐'
    ];

    protected $table = 'live';
    protected $guarded = ['id'];

}