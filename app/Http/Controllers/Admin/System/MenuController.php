<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/5
 * Time: 上午10:17
 */

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 菜单管理
 * Class MenuController
 * @package App\Http\Controllers\Admin\System
 */
class MenuController extends Controller
{
    /**
     * 菜单管理
     * @param Request $request
     */
    public function lists(Request $request) {
        $menu = Menu::getAll();
        $html = self::getTableHtml($menu);
        return view('admin.system.menu.lists', ['html' => $html]);
    }

    /**
     * 组合html
     * @param $menu  菜单内容
     * @param int $loop 层级
     * @return string
     */
    public function getTableHtml($menu, $loop = 0) {
        $html = '';
        if ($menu) {
            foreach ($menu as $val) {
                $pre = '';
                for ($i = 1; $i <= $loop; $i++) {
                    $pre .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                }
                if (isset($val['children'])) {
                    $pre .= '<i class="iconfont icon-jian" data-id="' . $val['id'] . '"></i>';
                } else {
                    $pre .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                }

                $html .= '<tr id="row_menu_id' . $val['id'] . '" class="menu_id' . $val['parent_id'] . '" data-id="' . $val['id'] . '">';
                $html .= '<td>' . $pre . '<i class="iconfont ' . $val['icon'] . '"></i>' . $val['title'] . '</td>';
                $html .= '<td>' . $val['url'] . '</td>';
                $html .= '<td align="center">' . $val['position'] . '</td>';
                $html .= '<td align="center"><input type="checkbox" name="sex" value="' . $val['id'] . '" lay-skin="switch" lay-text="' . Menu::STATUS_DESC[Menu::STATUS_ON] . '|' . Menu::STATUS_DESC[Menu::STATUS_ON] . '" lay-filter="status_btn" ' . ($val['status'] == Menu::STATUS_ON ? 'checked' : '') . '></td>';
                $html .= '<td>';
                if ($loop < Menu::LOOP_LEVEL) {
                    $html .= '<a class="layui-btn layui-btn-xs layui-btn-normal" lay-event="add_menu" data-id="' . $val['id'] . '">添加子分类</a>';
                }
                $html .= '<a class="layui-btn layui-btn-xs" lay-event="edit" data-id="' . $val['id'] . '">编辑</a>';
                $html .= '<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del" data-id="' . $val['id'] . '">删除</a>';
                $html .= '</td>';
                $html .= '</tr>';

                if (isset($val['children'])) {
                    $html .= self::getTableHtml($val['children'], $loop + 1);
                }
            }
        }
        return $html;
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
                'icon' => 'required',
                'parent_id' => 'numeric',
                'position' => 'required|numeric',
                'url' => 'required',
            ], [
                'title.required' => '菜单名称不能为空',
                'icon.required' => '图标不能为空',
                'parent_id.numeric' => '上级只能是数字',
                'position.required' => '排序不能为空',
                'position.numeric' => '排序只能是数字',
                'url.required' => '链接地址不能为空'
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['title', 'icon', 'parent_id', 'position', 'url']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }

            if ($request->id) {
                $res = Menu::where('id', $request->id)->update($save_data);
            } else {
                $result = Menu::create($save_data);
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
                $item = Menu::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            } else {
                $item['parent_id'] = (int)$request->parent_id;
            }
            return view('admin.system.menu.edit', ['item' => $item]);
        }
    }

    /**
     * 修改状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request) {
        $id = (int)$request->id;
        $status = (int)$request->status;
        if ($id && isset($status)) {
            $res = Menu::where('id', $id)->update(['status' => $status]);
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
        $id = (int)$request->id;

        if (!$id) {
            return res_error('参数错误');
        }

        //查询是否存在下级分类
        $sub_menu = Menu::where('parent_id', $id)->count();
        if ($sub_menu > 0) {
            return res_error('该菜单存在下级菜单，不能删除');
        }
        $res = Menu::where('id', $id)->delete();
        if ($res) {
            return res_success();
        } else {
            return res_error('删除失败');
        }
    }
}