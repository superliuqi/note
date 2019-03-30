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
 * 商品属性
 * Class Goods
 * @package App\Models
 */
class GoodsAttr extends Model
{

    protected $table = 'goods_attr';
    protected $guarded = ['id'];

    public $timestamps = false;

}