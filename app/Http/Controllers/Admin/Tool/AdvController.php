<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/11
 * Time: 下午1:09
 */

namespace App\Http\Controllers\Admin\Tool;

use App\Http\Controllers\Controller;
use App\Models\Adv;
use App\Models\AdvGroup;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 广告
 * Class AdvController
 * @package App\Http\Controllers\Admin\Tool
 */
class AdvController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        $group_id = $request->group_id;
        return view('admin.tool.adv.lists', ['group_id' => $group_id]);
    }

    /**
     * 列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listsAjax(Request $request) {
        $limit = $request->input('limit', 10);
        $keyword = $request->input('keyword');
        $group_id = (int)$request->group_id;
        if (!$group_id) {
            return res_error('用户组错误');
        }
        //搜索
        $where = array();
        $where[] = array('group_id', $group_id);
        if ($keyword) {
            $where[] = array('title', 'like', '%' . $keyword . '%');
        }
        $result = Adv::select('id', 'title', 'image', 'position', 'created_at', 'status')
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
                'title' => 'required',
                'group_id' => 'required|numeric',
                'target_type' => 'required',
                'target_value' => 'required',
                'position' => 'required|numeric',
                'start_at' => 'required|date_format:"Y-m-d H:i:s"',
                'end_at' => 'required|date_format:"Y-m-d H:i:s"',
            ], [
                'title.required' => '名称不能为空',
                'group_id.required' => '广告组不能为空',
                'group_id.numeric' => '广告组只能是数字',
                'target_type.required' => '跳转类型',
                'target_value.required' => '跳转url或id不能为空',
                'position.required' => '排序不能为空',
                'position.numeric' => '排序只能是数字',
                'start_at.required' => '开始时间不能为空',
                'start_at.date_format' => '开始时间格式错误',
                'end_at.required' => '结束时间不能为空',
                'end_at.date_format' => '结束时间格式错误',
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['title', 'group_id', 'image', 'target_type', 'target_value', 'position', 'start_at', 'end_at']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }

            if ($request->id) {
                $res = Adv::where('id', $request->id)->update($save_data);
            } else {
                $result = Adv::create($save_data);
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
                $item = Adv::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }
            $item['group_id'] = isset($item['group_id']) ? $item['group_id'] : $request->group_id;

            $adv_group = AdvGroup::find($item['group_id']);
            if (!$adv_group) {
                return res_error('广告组错误');
            }
            return view('admin.tool.adv.edit', ['item' => $item, 'adv_group' => $adv_group]);
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
            $res = Adv::whereIn('id', $ids)->update(['status' => $status]);
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

        $res = Adv::whereIn('id', $ids)->delete();
        if ($res) {
            return res_success();
        } else {
            return res_error('删除失败');
        }
    }

    /**
     * 修改排序
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function position(Request $request) {
        $id = (int)$request->id;
        $position = (int)$request->position;
        if ($id && isset($position)) {
            $res = Adv::where('id', $id)->update(['position' => $position]);
            if ($res) {
                return res_success();
            } else {
                return res_error('操作失败');
            }
        } else {
            return res_error('参数错误');
        }
    }
}