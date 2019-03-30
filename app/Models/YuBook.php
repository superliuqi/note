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
 * 图集
 * Class Article
 * @package App\Models
 */
class YuBook extends Model
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '待审'
    ];

    protected $table = 'yu_book';
    protected $guarded = ['id'];


    /**
     * 获取图片集合
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function image() {
        return $this->hasMany('App\Models\YuBookPhoto','book_id');
    }

}