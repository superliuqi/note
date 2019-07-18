<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/4/1
 * Time: 下午2:58
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;

class IndexController extends Controller
{

    /**
     * 后台首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        $user_data = Auth::guard('admin')->user();
        $role_right = AdminRole::adminRight($user_data['role_id']);//权限
        $menus = Menu::getMenu();
        $admin_menu = array();
        //读取菜单权限
        if ($user_data['username'] == 'admin') {
            $admin_menu = $menus;
        } else {
            foreach ($menus as $top_menu) {
                if (in_array($top_menu['id'], $role_right['top_menu'])) {
                    $_left_menu = array();
                    foreach ($top_menu['children'] as $left_menu) {
                        if (in_array($left_menu['id'], $role_right['left_menu'])) {
                            $_menu = array();
                            foreach ($left_menu['children'] as $menu) {
                                if (in_array($menu['url'], $role_right['menus'])) {
                                    $_menu[] = $menu;
                                }
                            }
                            $left_menu['children'] = $_menu;
                            $_left_menu[] = $left_menu;
                        }
                    }
                    $top_menu['children'] = $_left_menu;
                    $_menus[] = $top_menu;
                }
            }
            $admin_menu = $_menus;
        }
        return view('admin.index', array('menu' => $admin_menu, 'user_data' => $user_data, 'role_right' => $role_right));
    }

    /**
     * 后台右侧首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function main() {
        $system = array(
            'http_host' => $_SERVER["HTTP_HOST"],//网站域名
            'web_ip' => gethostbyname($_SERVER['SERVER_NAME']),//服务器ip
            'php_version' => PHP_VERSION,
            'server_soft' => $_SERVER['SERVER_SOFTWARE'],
            'php_path' => DEFAULT_INCLUDE_PATH,//php安装路径
            'file_size' => ini_get("file_uploads") ? ini_get("upload_max_filesize") : "Disabled"//文件最大上传限制
        );
        $data['system'] = $system;
        return view('admin.main', $data);
    }
}