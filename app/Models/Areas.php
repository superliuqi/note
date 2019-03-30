<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/15
 * Time: 上午11:05
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * 地区
 * Class Areas
 * @package App\Models
 */
class Areas extends Model
{
    protected $table = 'areas';
    protected $guarded = ['id'];

    /**
     * 根据id获取名称
     * @param string $id
     * @return string
     */
    public static function getAreaName($id = '') {
        $name = '';
        if ($id) {
            $area = self::find($id);
            if (isset($area['name'])) {
                $name =  $area['name'];
            }
        }
        return $name;
    }

    /**
     * 根据parent_id获取下级
     * @param string $parent_id 上级id
     * @return string
     */
    public static function getArea($parent_id = 0) {
        $area = array();
        $parent_id = (int)$parent_id;
        $area_res = self::where('parent_id', $parent_id)->pluck('name', 'id');
        if (!$area_res->isEmpty()) {
            $area = $area_res->toArray();
        }
        return $area;
    }
}