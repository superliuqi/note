<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/4/1
 * Time: 下午2:58
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    /**
     * 后台登陆
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        if ($request->isMethod('post')) {
            if (Auth::guard('admin')->attempt(['username' => $request->username, 'password' => $request->password, 'status' => AdminUser::STATUS_ON])) {
                return res_success();
            } else {
                return res_error('用户名或者密码不正确');
            }
        } else {
            if (Auth::guard('admin')->check()) {
                return redirect()->intended('index');
            } else {
                return view('admin.login');
            }
        }
    }

    /**
     * 退出登录
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function loginout(Request $request) {
        Auth::guard('admin')->logout();
        return redirect('login');
    }
}