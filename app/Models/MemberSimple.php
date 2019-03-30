<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/14
 * Time: 下午1:11
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 会员简介人物图
 * Class MemberGroup
 * @package App\Models
 */
class MemberSimple extends Model
{

    protected $table = 'member_simple';
    protected $guarded = ['id'];

    public $timestamps = false;

}