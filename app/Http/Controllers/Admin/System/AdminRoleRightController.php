<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/4/25
 * Time: 下午1:09
 */

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Models\AdminRoleRight;
use App\Models\Menu;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 管理员角色权限
 * Class AdminRoleRightController
 * @package App\Http\Controllers\Admin\System
 */
class AdminRoleRightController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        return view('admin.system.admin_role_right.lists');
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

        $result = AdminRoleRight::select('id', 'menu_child', 'title', 'created_at', 'status')
            ->where($where)
            ->orderBy('id', 'desc')
            ->paginate($limit)->toArray();
        if (!$result['data']) {
            return res_error('数据为空');
        }
        $menu_ids = array();
        foreach ($result['data'] as $value) {
            $menu_ids[] = $value['menu_child'];
        }
        if ($menu_ids) {
            $menu_res = Menu::whereIn('id', array_unique($menu_ids))->pluck('title', 'id');
            if (!$menu_res->isEmpty()) {
                $menu = $menu_res->toArray();
            }
        }
        $data_list = array();
        foreach ($result['data'] as $key => $value) {
            $_item = $value;
            $_item['menu'] = isset($menu[$value['menu_child']]) ? $menu[$value['menu_child']] : '';
            $data_list[] = $_item;
        }

        return res_success($data_list, $result['total']);
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
                'title' => 'required',
                'right' => 'required'
            ], [
                'title.required' => '名称不能为空',
                'right.required' => '权限码不能为空'
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['title', 'menu_top', 'menu_child']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }

            if ($request->right) {
                $right = explode(chr(10), $request->right);
                $rights = array();
                foreach ($right as $val) {
                    $_item = str_replace(chr(13), '', $val);
                    if ($_item && !in_array($_item, $rights)) {
                        $rights[] = $_item;
                    }
                }
                $save_data['right'] = join(',', $rights);
            }

            if ($request->id) {
                $res = AdminRoleRight::where('id', $request->id)->update($save_data);
            } else {
                $result = AdminRoleRight::create($save_data);
                $res = $result->id;
            }
            if ($res) {
                return res_success();
            } else {
                return res_error('保存失败');
            }
        } else {
            //查询菜单栏目
            $item = array();
            if ($request->id) {
                $item = AdminRoleRight::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
                $item['right'] = str_replace(',', chr(10), $item['right']);
            }
            //获取所有路由
            $app = app();
            $routes = $app->routes->getRoutes();
            $url_arr = array();
            foreach ($routes as $route){
                $action = $route->action;
                //只有后台的才进入
                if ($action['domain'] == config('app.admin_domain')) {
                    $prefix = explode('/', $route->uri);
                    $url_arr[$prefix[0]][] = $route->uri;
                }
            }
            foreach ($url_arr as $key => $val) {
                if (count($url_arr[$key]) <= 1) {
                    unset($url_arr[$key]);
                }
            }
            return view('admin.system.admin_role_right.edit', ['item' => $item, 'url_arr' => $url_arr]);
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
            $res = AdminRoleRight::whereIn('id', $ids)->update(['status' => $status]);
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

        $res = AdminRoleRight::whereIn('id', $ids)->delete();
        if ($res) {
            return res_success();
        } else {
            return res_error('删除失败');
        }
    }

    /**
     * 根据上级id获取菜单
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getMenu(Request $request) {
        $parent_id = (int)$request->parent_id;
        $menu = Menu::getMenuByParent($parent_id);
        return res_success($menu);
    }
}