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
 * 部门管理
 * Class MemberDepartment
 * @package App\Models
 */
class MemberDepartment extends Model
{


    const LOOP_LEVEL = 1;//最多层级

    protected $table = 'member_department';
    protected $guarded = ['id'];

    /**
     * 获取指定上级id下的所有菜单，按上下级排列
     * @param int $parent_id 上级id
     * @return array
     */
    protected function getAll($parent_id = 0) {
        $where = array(
            array('parent_id', $parent_id),
        );
        $result = self::select('id', 'title', 'parent_id')
            ->where($where)
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
        );
        $result = self::select('id', 'title')
            ->where($where)
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