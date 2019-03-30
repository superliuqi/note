<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

/**
 * 会员
 * Class Member
 * @package App\Models
 */
class Member extends User
{
    use SoftDeletes;
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '待审'
    ];

    protected $table = 'member';
    protected $guarded = ['id'];
    protected $hidden = ['password', 'pay_password', 'api_token', 'deleted_at'];

    protected $dates = ['deleted_at'];

    /**
     * 获取会员资料
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile() {
        return $this->hasOne('App\Models\MemberProfile');
    }

    /**
     * 获取会员组
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function group() {
        return $this->hasOne('App\Models\MemberGroup');
    }
}