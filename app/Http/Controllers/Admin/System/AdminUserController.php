<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/4/16
 * Time: 下午1:09
 */

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\AdminRole;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * 管理员管理
 * Class AdminUserController
 * @package App\Http\Controllers\Admin\System
 */
class AdminUserController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        return view('admin.system.admin_user.lists');
    }

    /**
     * 列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listsAjax(Request $request) {
        $limit = $request->input('limit', 10);
        $keyword = $request->input('keyword');

        //搜索
        $or_where = $where = array();
        if ($keyword) {
            $where[] = array('id', '=', $keyword);
            $or_where[] = array('username', 'like', '%' . $keyword . '%');
        }
        $result = AdminUser::select('id', 'username', 'created_at', 'status')
            ->where($where)
            ->orWhere($or_where)
            ->orderBy('id', 'desc')
            ->paginate($limit)->toArray();
        if (!$result['data']) {
            return res_error('数据为空');
        }
        return res_success($result['data'], $result['total']);
    }

    /**
     * 添加编辑
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request) {
        if ($request->isMethod('post')) {
            //验证规则
            $validator = Validator::make($request->all(), [
                'username' => [
                    'required',
                    Rule::unique('admin_user')->ignore($request->id)
                ],
                'role_id' => 'required|numeric',
                'tel' => 'required',
                'email' => 'required|email',
            ], [
                'username.required' => '用户名不能为空',
                'username.unique' => '用户已经存在',
                'role_id.required' => '角色不能为空',
                'role_id.numeric' => '角色只能是数字',
                'tel.required' => '电话不能为空',
                'email.required' => '邮箱地址不能为空',
                'email.email' => '邮箱格式错误'
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['username', 'role_id', 'tel', 'email']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }

            $password = $request->password;
            if ($password) {
                $save_data['password'] = Hash::make($password);
            }
            if ($request->id) {
                $res = AdminUser::where('id', $request->id)->update($save_data);
            } else {
                if (!$password) {
                    return res_error('密码不能为空');
                }
                $result = AdminUser::create($save_data);
                $res = $result->id;
            }
            if ($res) {
                return res_success();
            } else {
                return res_error('保存失败');
            }
        } else {
            $item = array();
            if ($request->id) {
                $item = AdminUser::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }

            //角色
            $role = AdminRole::where('status', AdminRole::STATUS_ON)
                ->select('id', 'title')
                ->get()->toArray();
            return view('admin.system.admin_user.edit', ['item' => $item, 'role' => $role]);
        }
    }

    /**
     * 修改状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request) {
        $id = $request->id;
        if (is_array($request->id)) {
            foreach ($id as $val) {
                $ids[] = (int)$val;
            }
        } else {
            $ids = array((int)$id);
        }
        $status = (int)$request->status;
        if ($ids && isset($status)) {
            $res = AdminUser::whereIn('id', $ids)->update(['status' => $status]);
            if ($res) {
                return res_success();
            } else {
                return res_error('操作失败');
            }
        } else {
            return res_error('参数错误');
        }
    }


    /**
     * 删除数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request) {
        $id = $request->id;
        if (is_array($request->id)) {
            foreach ($id as $val) {
                $ids[] = (int)$val;
            }
        } else {
            $ids = array((int)$id);
        }

        if (!$ids) {
            return res_error('参数错误');
        }

        $res = AdminUser::whereIn('id', $ids)->delete();
        if ($res) {
            return res_success();
        } else {
            return res_error('删除失败');
        }
    }

    /**
     * 修改资料
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function myEdit(Request $request) {
        $id = (int)auth()->id();
        if ($request->isMethod('post')) {
            //验证规则
            $validator = Validator::make($request->all(), [
                'tel' => 'required',
                'email' => 'required|email',
            ], [
                'tel.required' => '电话不能为空',
                'email.required' => '邮箱地址不能为空',
                'email.email' => '邮箱格式错误'
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['tel', 'email']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }

            $password = $request->input('password');
            if ($password) {
                $save_data['password'] = Hash::make($request->password);
            }

            if ($id) {
                $res = AdminUser::where('id', $id)->update($save_data);
            }
            if ($res) {
                return res_success();
            } else {
                return res_error('保存失败');
            }
        } else {
            $item = array();
            if ($id) {
                $item = AdminUser::find($id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }
            return view('admin.system.admin_user.my_edit', ['item' => $item]);
        }
    }

}