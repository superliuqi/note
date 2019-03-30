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
 * 商品评论
 * Class Goods
 * @package App\Models
 */
class CommentGoods extends Model
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '待审'
    ];


    protected $table = 'comment_goods';
    protected $guarded = ['id'];

    /**
     *获取状态
     * @param string $status
     * @return mixed|string
     */
    public static function getStatus($status = '')
    {
        $status_desc = self::STATUS_DESC;
        return array_key_exists($status, $status_desc) ? $status_desc[$status] : '';
    }
    
}