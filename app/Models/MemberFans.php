<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 会员粉丝
 * Class MemberProfile
 * @package App\Models
 */
class MemberFans extends Model
{
    //类型
    const TYPE_USER = 0;
    const TYPE_DOCTOR = 1;
    const TYPE_ORGANIZE = 2;
    const TYPE_DESIGNER = 3;

    protected $table = 'member_fans';
    protected $guarded = [];

}