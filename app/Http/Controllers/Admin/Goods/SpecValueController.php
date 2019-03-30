<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/11
 * Time: 下午1:09
 */

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Controller;
use App\Models\Spec;
use App\Models\SpecValue;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 商品规格值管理
 * Class AdvController
 * @package App\Http\Controllers\Admin\Tool
 */
class SpecValueController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        $spec_id = $request->spec_id;
        return view('admin.goods.spec_value.lists', ['spec_id' => $spec_id]);
    }

    /**
     * 列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listsAjax(Request $request) {
        $limit = $request->input('limit', 10);
        $keyword = $request->input('keyword');
        $spec_id = (int)$request->spec_id;
        if (!$spec_id) {
            return res_error('规格错误');
        }
        //搜索
        $where = array();
        $where[] = array('spec_id', $spec_id);
        if ($keyword) {
            $where[] = array('value', 'like', '%' . $keyword . '%');
        }
        $result = SpecValue::select('id', 'value', 'position', 'created_at')
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
                'value' => 'required',
                'spec_id' => 'required|numeric',
                'position' => 'required|numeric',
            ], [
                'value.required' => '规格值不能为空',
                'spec_id.required' => '规格id不能为空',
                'spec_id.numeric' => '规格id只能是数字',
                'position.required' => '排序不能为空',
                'position.numeric' => '排序只能是数字',
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['value', 'spec_id', 'position']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }

            if ($request->id) {
                $res = SpecValue::where('id', $request->id)->update($save_data);
            } else {
                $result = SpecValue::create($save_data);
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
                $item = SpecValue::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }
            $item['spec_id'] = isset($item['spec_id']) ? $item['spec_id'] : $request->spec_id;
            $spec = Spec::find($item['spec_id']);
            if (!$spec) {
                return res_error('规格错误');
            }
            return view('admin.goods.spec_value.edit', ['item' => $item]);
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

        $res = SpecValue::whereIn('id', $ids)->delete();
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
            $res = SpecValue::where('id', $id)->update(['position' => $position]);
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