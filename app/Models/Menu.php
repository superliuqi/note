<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/4/14
 * Time: 上午10:03
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * 后台菜单
 * Class Menu
 * @package App\Models
 */
class Menu extends Model
{
    //状态
    const STATUS_ON = 1;
    const STATUS_OFF = 0;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '待审'
    ];

    const LOOP_LEVEL = 2;//最多层级

    protected $table = 'menu';
    protected $guarded = ['id'];

    /**
     * 获取指定上级id下的所有菜单，按上下级排列(获取管理菜单)
     * @param int $parent_id 上级id
     * @return array
     */
    protected function getMenu($parent_id = 0) {
        $where = array(
            array('parent_id', $parent_id),
            array('status', self::STATUS_ON),
        );
        $result = self::select('id', 'title', 'icon', 'url')
            ->where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get()->toArray();
        $return_list = array();
        if ($result) {
            foreach ($result as $key => $value) {
                $_item = array(
                    'id' => $value['id'],
                    'title' => $value['title'],
                    'icon' => $value['icon'],
                    'href' => url($value['url']),
                    'url' => $value['url'],
                    'spread' => true,
                );
                $child = self::getMenu($value['id']);
                if ($child) {
                    $_item['children'] = $child;
                }
                $return_list[] = $_item;
            }
        }
        return $return_list;
    }

    /**
     * 获取指定上级id下的所有菜单，按上下级排列（后台管理）
     * @param int $parent_id 上级id
     * @return array
     */
    protected function getAll($parent_id = 0) {
        $where = array(
            array('parent_id', $parent_id),
        );
        $result = self::select('id', 'title', 'icon', 'url', 'parent_id', 'position', 'status')
            ->where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get()->toArray();
        $return_list = array();
        if ($result) {
            foreach ($result as $key => $value) {
                $_item = array(
                    'id' => $value['id'],
                    'title' => $value['title'],
                    'icon' => $value['icon'],
                    'url' => url($value['url']),
                    'parent_id' => $value['parent_id'],
                    'position' => $value['position'],
                    'status' => $value['status']
                );
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
     * 获取指定上级id下的菜单
     * @param int $parent_id 上级id
     * @return array
     */
    protected function getMenuByParent($parent_id = 0) {
        $where = array(
            array('parent_id', $parent_id),
            array('status', self::STATUS_ON),
        );
        $result = self::select('id', 'title')
            ->where($where)
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get()->toArray();
        return $result;
    }
}