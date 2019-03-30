<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 支付方式
 * Class PaymentController
 * @package App\Http\Controllers\Admin\System
 */
class PaymentController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        return view('admin.system.payment.lists');
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
        $result = Payment::select('id', 'title', 'logo', 'desc', 'position', 'type', 'client_type', 'created_at', 'status')
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
            $_item['client_type'] = Payment::CLIENT_TYPE_DESC[$value['client_type']];
            $_item['type'] = Payment::TYPE_DESC[$value['type']];
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
                'client_type' => 'required|numeric',
                'position' => 'required|numeric',
            ], [
                'title.required' => '标题不能为空',
                'type.required' => '类型不能为空',
                'type.numeric' => '类型只能是数字',
                'client_type.required' => '客户端类型不能为空',
                'client_type.numeric' => '客户端类型只能是数字',
                'position.required' => '排序不能为空',
                'position.numeric' => '排序只能是数字',
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['title', 'logo', 'type', 'client_type', 'desc', 'position']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }

            if ($request->id) {
                $res = Payment::where('id', $request->id)->update($save_data);
            } else {
                $result = Payment::create($save_data);
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
                $item = Payment::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }
            return view('admin.system.payment.edit', ['item' => $item]);
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
            $res = Payment::whereIn('id', $ids)->update(['status' => $status]);
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
     * 修改排序
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function position(Request $request) {
        $id = (int)$request->id;
        $position = (int)$request->position;
        if ($id && isset($position)) {
            $res = Payment::where('id', $id)->update(['position' => $position]);
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