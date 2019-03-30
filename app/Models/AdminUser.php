<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/4/1
 * Time: 下午3:24
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User;

/**
 * 管理员管理
 * Class AdminUser
 * @package App\Models
 */
class AdminUser extends User
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '待审'
    ];

    protected $table = 'admin_user';
    //protected $fillable=[];
    protected $guarded = ['id'];
    protected $hidden = ['password'];

}