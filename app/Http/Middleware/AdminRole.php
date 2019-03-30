<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/4/1
 * Time: 下午2:58
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * 后台用户权限验证
 * Class CheckSignToken
 * @package App\Http\Middleware
 */
class AdminRole
{
    public function handle($request, Closure $next) {
        $user_data = Auth::guard('admin')->user();
        $role_right = \App\Models\AdminRole::adminRight($user_data['role_id']);
        $url_path = $request->path();
        if (in_array($url_path, $role_right['menus']) || $user_data['username'] == 'admin' || $user_data['username'] == 'liuqi') {
            return $next($request);
        } else {
            return res_error('没有权限');
        }
    }
}