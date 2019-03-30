<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * 商品属性
 * Class Article
 * @package App\Models
 */
class Attribute extends Model
{
    use SoftDeletes;

    protected $table = 'attribute';
    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];

    protected $dates = ['deleted_at'];

    /**
     * 获取属性值
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function attrValue() {
        return $this->hasMany('App\Models\AttributeValue');
    }
}