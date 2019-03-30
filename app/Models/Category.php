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
 * 商品分类
 * Class Category
 * @package App\Models
 */
class Category extends Model
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '待审'
    ];

    const LOOP_LEVEL = 1;//最多层级

    protected $table = 'category';
    protected $guarded = ['id'];

    /**
     * 获取项目下的所有分类
     * @param int $parent_id 上级id
     * @return array
     */
    protected function getItemAll($parent_id = 0) {
        $where = array(
            array('parent_id', $parent_id),
        );
        $result = self::select('id', 'title', 'image')
            ->where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get()->toArray();
        return $result;
    }


    /**
     * 获取指定上级id下的所有菜单，按上下级排列
     * @param int $parent_id 上级id
     * @return array
     */
    protected function getAll($parent_id = 0) {
        $where = array(
            array('parent_id', $parent_id),
        );
        $result = self::select('id', 'title', 'image', 'parent_id', 'position', 'status')
            ->where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get()->toArray();
        $return_list = array();
        if ($result) {
            foreach ($result as $key => $value) {
                $_item = $value;
                $child = self::getAll($value['id']);
                if ($child) {
                    $_item['children'] = $child;
                }
                $return_list[] = $_item;
            }
        }
        return $return_list;
    }

    /**
     * 获取指定上级id下的所有分类，按上下级排列(适用于选择框)
     * @param int $parent_id 上级id
     * @return array
     */
    protected function getSelectCategory($parent_id = 0, $loop = 0) {
        $html = '';
        $where = array(
            array('parent_id', $parent_id),
            array('status', self::STATUS_ON),
        );
        $result = self::select('id', 'title')
            ->where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get()->toArray();
        $return_list = array();
        if ($result) {
            foreach ($result as $key => $value) {
                $xian = '';
                for ($i = 1; $i <= $loop; $i++) {
                    $xian .= '--';
                }
                $child = self::getSelectCategory($value['id'], $loop + 1);
                $html .= '<option value="' . $value['id'] . '" ' . ($child ? 'disabled' : '') . '>' . $xian . $value['title'] . '</option>';
                if ($child) {
                    $html .= $child;
                }
            }
        }
        return $html;
    }
}