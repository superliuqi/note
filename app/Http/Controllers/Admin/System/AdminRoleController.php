<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/4/16
 * Time: 下午1:09
 */

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Models\AdminRoleRight;
use App\Models\Menu;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 管理员角色
 * Class AdminRoleController
 * @package App\Http\Controllers\Admin\System
 */
class AdminRoleController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        return view('admin.system.admin_role.lists');
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
        $where = array();
        if ($keyword) {
            $where[] = array('title', 'like', '%' . $keyword . '%');
        }
        $result = AdminRole::select('id', 'title', 'created_at', 'status')
            ->where($where)
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
                'title' => [
                    'required',
                    Rule::unique('admin_role')->ignore($request->id)
                ],
                'right' => 'required',
            ], [
                'title.required' => '角色名称不能为空',
                'title.unique' => '角色名称已经存在',
                'right.required' => '权限不能为空'
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['title']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }
            $save_data['right'] = json_encode($request->right);

            if ($request->id) {
                $res = AdminRole::where('id', $request->id)->update($save_data);
            } else {
                $result = AdminRole::create($save_data);
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
                $item = AdminRole::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
                $item['right'] = json_decode($item['right'], true);
            }
            $role_right = array();
            $menu_ids = array();
            //获取权限列表
            $rights = AdminRoleRight::all()->toArray();
            if ($rights) {
                foreach ($rights as $right) {
                    $menu_ids[] = $right['menu_top'];
                    $menu_ids[] = $right['menu_child'];
                    $role_right[$right['menu_top']][$right['menu_child']][] = $right;
                }
                //菜单名称
                if ($menu_ids) {
                    $menu_res = Menu::whereIn('id', array_unique($menu_ids))->get()->toArray();
                    $menus = array_column($menu_res, 'title', 'id');
                }
            }
            return view('admin.system.admin_role.edit', ['item' => $item, 'role_right' => $role_right, 'menus' => $menus]);
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
            $res = AdminRole::whereIn('id', $ids)->update(['status' => $status]);
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

        $res = AdminRole::whereIn('id', $ids)->delete();
        if ($res) {
            return res_success();
        } else {
            return res_error('删除失败');
        }
    }
}