<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Jobs\DeliveryRemind;
use App\Models\ExpressCompany;
use App\Models\Member;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\Payment;
use App\Models\Seller;
use App\Service\OrderService;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 订单管理
 * Class OrderController
 * @package App\Http\Controllers\Admin\System
 */
class OrderController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        $seller = Seller::all()->pluck('title', 'id')->toArray();
        $return  = array(
            'seller' => $seller
        );
        return view('admin.order.order.lists', $return);
    }

    /**
     * 列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listsAjax(Request $request) {
        $limit = $request->input('limit', 10);
        $id = (int)$request->input('id');
        $order_no = $request->input('order_no');
        $full_name = $request->input('full_name');
        $tel = $request->input('tel');
        $username = $request->input('username');
        $seller_id = (int)$request->input('seller_id');
        $status = $request->input('status');

        //搜索
        $where = array();
        if ($id) $where[] = array('id', $id);
        if ($order_no) $where[] = array('order_no', $order_no);
        if ($full_name) $where[] = array('full_name', $full_name);
        if ($tel) $where[] = array('tel', $tel);
        if ($seller_id) $where[] = array('seller_id', $seller_id);
        if (is_numeric($status)) $where[] = array('status', $status);

        if ($username) {
            $search_member = Member::where('username', $username)->first();
            if ($search_member) {
                $where[] = array('m_id', $search_member['id']);
            } else {
                return res_error('数据为空');
            }
        }

        $result = Order::select('id', 'm_id', 'order_no', 'payment_id', 'full_name', 'tel', 'status', 'pay_at', 'created_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->paginate($limit)->toArray();
        if (!$result['data']) {
            return res_error('数据为空');
        }
        //查询用户和支付方式
        $m_ids = $payment_ids = array();
        foreach ($result['data'] as $value) {
            $m_ids[] = $value['m_id'];
            $payment_ids[] = $value['payment_id'];
        }
        if ($m_ids) {
            $member_res = Member::whereIn('id', array_unique($m_ids))->pluck('username', 'id');
            if (!$member_res->isEmpty()) {
                $member_data = $member_res->toArray();
            }
        }
        if ($payment_ids) {
            $payment_res = Payment::whereIn('id', array_unique($payment_ids))->pluck('title', 'id');
            if (!$payment_res->isEmpty()) {
                $payment_data = $payment_res->toArray();
            }
        }
        $data_list = array();
        foreach ($result['data'] as $value) {
            $_item = $value;
            $_item['status'] = Order::STATUS_DESC[$value['status']];
            $_item['username'] = isset($member_data[$value['m_id']]) ? $member_data[$value['m_id']] : '';
            $_item['payment'] = isset($payment_data[$value['payment_id']]) ? $payment_data[$value['payment_id']] : '';
            $data_list[] = $_item;
        }
        return res_success($data_list, $result['total']);
    }

    /**
     * 订单详情
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail(Request $request) {
        $id  = (int)$request->id;
        $order = Order::find($id);

        if (!$order) {
            return res_error('订单不存在');
        }
        $order['status_label'] = Order::STATUS_DESC[$order['status']];
        $order['delivery_type'] = Order::DELIVERY_DESC[$order['delivery_type']];
        $order['payment_name'] = $order['payment_id'] ? Payment::find($order['payment_id'])->value('title') : '';
        $order['username'] = $order['m_id'] ? Member::find($order['m_id'])->value('username') : '';
        $order['seller'] = $order['seller_id'] ? Seller::find($order['seller_id'])->value('title') : '';
        //获取订单商品
        $order_goods = array();
        $order_goods_res = $order->goods()
            ->select('id', 'goods_title', 'image', 'sku_code', 'sell_price', 'market_price', 'buy_qty', 'spec_value', 'delivery', 'refund')
            ->where('order_id', $id)
            ->orderBy('id', 'desc')
            ->get();
        if ($order_goods_res->isEmpty()) {
            return res_error('订单商品不存在');
        }
        foreach ($order_goods_res->toArray() as $value) {
            $_item = $value;
            $_item['sell_price'] = '￥' . $value['sell_price'];
            $_item['delivery_text'] = OrderGoods::DELIVERY_DESC[$value['delivery']];
            $_item['refund'] = OrderGoods::REFUND_DESC[$value['refund']];
            $order_goods[] = $_item;
        }
        //物流信息
        $delivery = array();
        $delivery_res = $order->delivery()
            ->select('company_name', 'code', 'note', 'created_at')
            ->where('order_id', $id)
            ->orderBy('id', 'desc')
            ->get();
        if (!$delivery_res->isEmpty()) {
            $delivery = $delivery_res->toArray();
        }
        //订单日志
        $log = array();
        $log_res = $order->log()
            ->select('username', 'action', 'note', 'created_at')
            ->where('order_id', $id)
            ->orderBy('id', 'desc')
            ->get();
        if (!$log_res->isEmpty()) {
            $log = $log_res->toArray();
        }
        //物流公司
        $express_company = ExpressCompany::where('status', ExpressCompany::STATUS_ON)->pluck('title', 'id');

        $return_data = array(
            'order_goods' => $order_goods,
            'order' => $order,
            'delivery' => $delivery,
            'log' => $log,
            'express_company' => $express_company
        );
        return view('admin.order.order.detail', $return_data);
    }

    /**
     * 订单发货
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delivery(Request $request) {
        //验证规则
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'order_goods_id' => 'required|array',
            'order_goods_id[]' => 'numeric',
            'company_id' => 'required',
            'code' => 'required',
        ], [
            'id.required' => '订单ID不能为空',
            'id.numeric' => '订单ID只能是数字',
            'order_goods_id.required' => '发货商品不能为空',
            'order_goods_id.array' => '发货商品不能为空',
            'order_goods_id[].numeric' => '发货商品不能为空',
            'company_id.required' => '物流ID不能为空',
            'code.required' => '物流单号不能为空',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            return res_error(current($error));
        }
        $order = Order::find($request->id);

        if (!$order) {
            return res_error('订单不存在');
        }
        if (!OrderService::isDelivery($order)) {
            return res_error('订单已发货或不满足发货条件');
        }

        //开始发货
        $admin_user = auth()->user();
        $res = OrderService::delivery($request->id, $request->order_goods_id, $request->company_id, $request->code, $admin_user['username'], $admin_user['id'], 0, $request->note);
        if ($res) {
//            Redis::rpush('order_delivery' , $request->id);
            DeliveryRemind::dispatch($order['m_id'])->delay(now()->addMinutes(2));
            return res_success();
        } else {
            return res_error('发货失败');
        }
    }

    /**
     * 订单取消
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cancel(Request $request) {
        //验证规则
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ], [
            'id.required' => '订单ID不能为空',
            'id.numeric' => '订单ID只能是数字',
        ]);
        $error = $validator->errors()->all();
        if ($error) {
            return res_error(current($error));
        }
        $order = Order::find($request->id);

        if (!$order) {
            return res_error('订单不存在');
        }
        if (!OrderService::isCancel($order)) {
            return res_error('订单不满足取消条件');
        }

        //开始取消
        $admin_user = auth()->user();
        $res = OrderService::cancel($request->id, $admin_user['username'], $admin_user['id'], 0);
        if ($res) {
            return res_success();
        } else {
            return res_error('取消失败');
        }

    }
}