<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/4/17
 * Time: 下午4:46
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 管理员角色
 * Class AdminRole
 * @package App\Models
 */
class AdminRole extends Model
{
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '待审'
    ];

    protected $table = 'admin_role';
    protected $guarded = ['id'];

    /**
     * 获取用户角色的具体权限
     * @param $role_id 角色id
     * @param $is_refresh 是否刷新缓存
     */
    public static function adminRight($role_id, $is_refresh = 0) {
        $return = get_redis_array('admin_user_role:' . $role_id);
        if (!$return || $is_refresh) {
            $return = array(
                'top_menu' => [],
                'left_menu' => [],
                'menus' => []
            );
            $role = self::where('id', $role_id)->value('right');
            if (!$role) {
                return $return;
            }
            $role_right = json_decode($role, true);

            $return['top_menu'] = array_keys($role_right);
            $menus = $left_menu = array();
            foreach ($role_right as $group_menu) {
                foreach ($group_menu as $key => $menu) {
                    $left_menu[] = $key;
                    foreach ($menu as $right) {
                        $_right = explode(',', $right);
                        $menus = array_merge($_right, $menus);
                    }
                }
            }
            $return['left_menu'] = $left_menu;
            $return['menus'] = $menus;
            set_redis_array('admin_user_role:' . $role_id, $return, 300);
        }
        return $return;
    }
}