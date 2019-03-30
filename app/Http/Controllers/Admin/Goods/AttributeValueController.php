<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/11
 * Time: 下午1:09
 */

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 商品属性值管理
 * Class AdvController
 * @package App\Http\Controllers\Admin\Tool
 */
class AttributeValueController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        $attribute_id = $request->attribute_id;
        return view('admin.goods.attribute_value.lists', ['attribute_id' => $attribute_id]);
    }

    /**
     * 列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listsAjax(Request $request) {
        $limit = $request->input('limit', 10);
        $keyword = $request->input('keyword');
        $attribute_id = (int)$request->attribute_id;
        if (!$attribute_id) {
            return res_error('属性错误');
        }
        //搜索
        $where = array();
        $where[] = array('attribute_id', $attribute_id);
        if ($keyword) {
            $where[] = array('value', 'like', '%' . $keyword . '%');
        }
        $result = AttributeValue::select('id', 'value', 'position', 'created_at')
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
                'attribute_id' => 'required|numeric',
                'position' => 'required|numeric',
            ], [
                'value.required' => '属性值不能为空',
                'attribute_id.required' => '属性id不能为空',
                'attribute_id.numeric' => '属性id只能是数字',
                'position.required' => '排序不能为空',
                'position.numeric' => '排序只能是数字',
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['value', 'attribute_id', 'position']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }

            if ($request->id) {
                $res = AttributeValue::where('id', $request->id)->update($save_data);
            } else {
                $result = AttributeValue::create($save_data);
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
                $item = AttributeValue::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }
            $item['attribute_id'] = isset($item['attribute_id']) ? $item['attribute_id'] : $request->attribute_id;
            $attribute = Attribute::find($item['attribute_id']);
            if (!$attribute) {
                return res_error('属性错误');
            }
            return view('admin.goods.attribute_value.edit', ['item' => $item]);
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

        $res = AttributeValue::whereIn('id', $ids)->delete();
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
            $res = AttributeValue::where('id', $id)->update(['position' => $position]);
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