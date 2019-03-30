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
 * 商品详情
 * Class ArticleDesc
 * @package App\Models
 */
class GoodsDesc extends Model
{

    protected $table = 'goods_desc';
    protected $guarded = [];

    public $timestamps = false;

}