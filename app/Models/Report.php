<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:25
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 用户举报
 * Class Payment
 * @package App\Models
 */
class Report extends Model
{

    protected $table = 'report';
    protected $guarded = ['id'];

}