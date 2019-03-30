<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/5
 * Time: 上午10:04
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 系统设置
 * Class Config
 * @package App\Models
 */
class Config extends Model
{
    const TAB_NAME = ['基本设置', '微信设置', '更新设置', '系统设置', '直播设置', '其他设置'];

    protected $table = 'config';
    protected $guarded = ['id'];
}