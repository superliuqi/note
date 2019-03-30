<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Controller;
use App\Models\Areas;
use App\Models\Delivery;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 配送方式
 * Class DeliveryController
 * @package App\Http\Controllers\Admin\System
 */
class DeliveryController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        return view('admin.goods.delivery.lists');
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
            $or_where[] = array('title', 'like', '%' . $keyword . '%');
        }
        $result = Delivery::select('id', 'title', 'open_default', 'price_type', 'desc', 'status', 'created_at')
            ->where($where)
            ->orWhere($or_where)
            ->orderBy('id', 'desc')
            ->paginate($limit)->toArray();
        if (!$result['data']) {
            return res_error('数据为空');
        }
        $data_list = array();
        foreach ($result['data'] as $key => $value) {
            $_item = $value;
            $_item['open_default'] = Delivery::OPEN_DEFAULT_DESC[$value['open_default']];
            $_item['price_type'] = Delivery::PRICE_TYPE_DESC[$value['price_type']];
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
                'type' => 'required|numeric',
                'free_type' => 'required|numeric',
                'free_price' => 'required',
                'first_weight' => 'required|numeric',
                'first_price' => 'required',
                'second_weight' => 'required|numeric',
                'second_price' => 'required',
                'price_type' => 'required|numeric',
            ], [
                'title.required' => '标题不能为空',
                'type.required' => '类型不能为空',
                'type.numeric' => '类型只能是数字',
                'free_type.required' => '包邮类型不能为空',
                'free_type.numeric' => '包邮类型只能是数字',
                'free_price.required' => '包邮金额/件不能为空',
                'first_weight.required' => '首重/件数不能为空',
                'first_weight.numeric' => '首重/件数只能是数字',
                'first_price.required' => '首重/件费用不能为空',
                'second_weight.required' => '续重/件数不能为空',
                'second_weight.numeric' => '续重/件数只能是数字',
                'second_price.required' => '续重/件费用不能为空',
                'price_type.required' => '费用类型不能为空',
                'price_type.numeric' => '费用类型只能是数字',
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['title', 'type', 'free_type', 'free_price', 'first_weight', 'first_price', 'second_weight', 'second_price', 'price_type', 'open_default']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }
            $save_data['seller_id'] = 1;
            if (!isset($save_data['open_default'])) $save_data['open_default'] = Delivery::OPEN_DEFAULT_OFF;

            if ($request->group_area_id) {
                $group_area_id = array();
                $group_json = array();
                foreach ($request->group_area_id as $key => $value) {
                    if ($value) {
                        $group_area_id[] = array_values($value);
                        $_item = array(
                            'type' => $request->group_type[$key],
                            'free_type' => $request->group_free_type[$key],
                            'free_price' => $request->group_free_price[$key],
                            'first_weight' => $request->group_first_weight[$key],
                            'first_price' => $request->group_first_price[$key],
                            'second_weight' => $request->group_second_weight[$key],
                            'second_price' => $request->group_second_price[$key],
                        );
                        $group_json[] = $_item;
                    }
                }
                $save_data['group_area_id'] = json_encode($group_area_id);
                $save_data['group_json'] = json_encode($group_json);
            }
            if ($request->id) {
                $res = Delivery::where('id', $request->id)->update($save_data);
            } else {
                $result = Delivery::create($save_data);
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
                $item = Delivery::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
                //分组地区信息
                $select_area_id = array();
                $group_data = array();
                $group_area_id = json_decode($item['group_area_id'], true);
                $group_json = json_decode($item['group_json'], true);
                if ($group_area_id && $group_json) {
                    foreach ($group_json as $key => $value) {
                        $value['list_id'] = $key;
                        $select_area_id = array_merge($select_area_id, $group_area_id[$key]);
                        $group_data[] = $value;
                        //分组地区里面回填信息
                        $item['group_area_id['.$key.'][]'] = join(';', $group_area_id[$key]);
                        $item['group_type['.$key.']'] = $value['type'];
                        $item['group_free_type['.$key.']'] = $value['free_type'];

                    }
                }
                $item['select_area_id'] = $select_area_id;
                $item['group_data'] = $group_data;
            }
            $prov_list = Areas::getArea();
            return view('admin.goods.delivery.edit', ['item' => $item, 'prov_list' => $prov_list]);
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
            $res = Delivery::whereIn('id', $ids)->update(['status' => $status]);
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

        $res = Delivery::whereIn('id', $ids)->delete();
        if ($res) {
            return res_success();
        } else {
            return res_error('删除失败');
        }
    }

}