<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/4/17
 * Time: 下午4:46
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 管理员角色权限
 * Class AdminRoleRight
 * @package App\Models
 */
class AdminRoleRight extends Model
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '待审'
    ];

    protected $table = 'admin_role_right';
    protected $guarded = ['id'];

}