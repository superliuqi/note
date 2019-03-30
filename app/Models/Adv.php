<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/11
 * Time: 下午4:46
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 广告
 * Class Adv
 * @package App\Models
 */
class Adv extends Model
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '待审'
    ];

    //跳转连接类型
    const TARGET_TYPE_URL = 1;
    const TARGET_TYPE_ARTICLE = 2;
    const TARGET_TYPE_VIDEO = 3;
    const TARGET_TYPE_DESC = [
        self::TARGET_TYPE_URL => 'url',
        self::TARGET_TYPE_ARTICLE => '文章',
        self::TARGET_TYPE_VIDEO => '视频'
    ];

    protected $table = 'adv';
    protected $guarded = ['id'];

    //获取广告位内容
    public static function getAdv($code) {
        $return = array();
        if ($code) {
            $group_id = AdvGroup::where([['status', AdvGroup::STATUS_ON], ['code', $code]])->value('id');
            if ($group_id) {
                $where = ['status' => self::STATUS_ON, 'group_id' => $group_id];
                $adv_res = self::select('id', 'title', 'image', 'target_type', 'target_value')
                    ->where($where)
                    ->orderBy('position', 'asc')
                    ->orderBy('id', 'desc')
                    ->get();
                if ($adv_res->isEmpty()) {
                    return $return;
                } else {
                    $adv = $adv_res->toArray();
                    return $adv;
                }
            } else {
                return $return;
            }
        }
        return $return;
    }
}