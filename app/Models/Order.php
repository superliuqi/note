<?php
    /**
     * Created by PhpStorm.
     * User: wanghui
     * Date: 2018/6/4
     * Time: 下午1:25
     */

    namespace App\Models;

    use App\Service\OrderService;
    use App\Service\PushService;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use App\Models\CouponsDetail;

    /**
     * 订单
     * Class Payment
     * @package App\Models
     */
    class Order extends Model
    {
        //状态
        const STATUS_WAIT_PAY = 0;
        const STATUS_PAID = 1;
        const STATUS_SHIPMENT = 2;
        const STATUS_DONE = 3;
        const STATUS_COMMENT = 4;
        const STATUS_COMPLETE = 5;
        const STATUS_REFUND_COMPLETE = 6;
        const STATUS_CANNEL = 10;
        const STATUS_SYSTEM_CANNEL = 11;

        //后台展示状态
        const STATUS_DESC = [
            self::STATUS_WAIT_PAY        => '待支付',
            self::STATUS_PAID            => '已支付',
            self::STATUS_SHIPMENT        => '待收货',
            self::STATUS_DONE            => '待评价',
            self::STATUS_COMMENT         => '已评价',
            self::STATUS_COMPLETE        => '订单完成',
            self::STATUS_REFUND_COMPLETE => '全部退款',
            self::STATUS_CANNEL          => '已取消',
            self::STATUS_SYSTEM_CANNEL   => '后台取消',
        ];

        //用户展示状态
        const STATUS_MEMBER_DESC = [
            self::STATUS_WAIT_PAY        => '待支付',
            self::STATUS_PAID            => '待发货',
            self::STATUS_SHIPMENT        => '待收货',
            self::STATUS_DONE            => '待评价',
            self::STATUS_COMMENT         => '已评价',
            self::STATUS_COMPLETE        => '订单完成',
            self::STATUS_REFUND_COMPLETE => '已退款',
            self::STATUS_CANNEL          => '已取消',
            self::STATUS_SYSTEM_CANNEL   => '已取消',
        ];

        //配送方式
        const DELIVERY_SEND = 0;
        const DELIVERY_USER = 1;

        const DELIVERY_DESC = [
            self::DELIVERY_SEND => '快递',
            self::DELIVERY_USER => '自提',
        ];

        //风险订单提示
        const FLAG_NO = 0;
        const FLAG_YES = 1;

        const FLAG_DESC = [
            self::FLAG_NO  => '正常',
            self::FLAG_YES => '风险',
        ];

        //订单类型
        const GOODS_TYPE = 1;
        const SCORE_TYPE = 2;

        protected $table = 'order';
        protected $guarded = ['id'];

        /**
         * 获取订单按钮
         */
        public static function getOrderButton($order_data = [])
        {
            $button = [];
            if ($order_data) {
                $button['wait_pay'] = OrderService::isPay($order_data) ? 1 : 0;
                $button['cancel'] = OrderService::isCancel($order_data) ? 1 : 0;
//            $button['sended'] = OrderService::isDelivery($order_data)?1:0;
                $button['confirmed'] = OrderService::isConfirm($order_data) ? 1 : 0;
                $button['comment'] = OrderService::isComment($order_data) ? 1 : 0;
                $button['refund'] = OrderService::isRefund($order_data) ? 1 : 0;;

                return $button;
            } else {
                return false;
            }
        }

        /**
         * 获取商品
         * @return \Illuminate\Database\Eloquent\Relations\hasMany
         */
        public function goods()
        {
            return $this->hasMany('App\Models\OrderGoods');
        }

        /**
         * 获取发货信息
         * @return \Illuminate\Database\Eloquent\Relations\hasMany
         */
        public function delivery()
        {
            return $this->hasMany('App\Models\OrderDelivery');
        }

        /**
         * 获取订单日志
         * @return \Illuminate\Database\Eloquent\Relations\hasMany
         */
        public function log()
        {
            return $this->hasMany('App\Models\OrderLog');
        }

        /**
         * 添加订单
         * @param array $order_data
         */
        public static function addOrder($order_data = [])
        {
            if ($order_data) {
                try {
                    DB::transaction(function () use ($order_data) {
                        $order_ids = [];
                        foreach ($order_data as $order) {
                            $order_goods = isset($order['product']) ? $order['product'] : [];
                            if (!$order_goods) return false;
                            unset($order['product']);
                            $order_res = self::create($order);
                            $order_id = $order_res->id;
                            foreach ($order_goods as $product) {
                                $product['order_id'] = $order_id;
                                OrderGoods::create($product);
                            }
                        }
                    });

                    return true;
                } catch (\Exception $e) {
                    return false;
                }
            }

            return false;
        }


        /**
         * 系统自动取消订单
         * @param $order_id
         */
        public static function orderAutoCancel($order_id)
        {
            if (!empty($order_id)) {
                $order_data = self::where('id', $order_id)->first();
                if ($order_data && $order_data['status'] == '0') {
                    try {
                        DB::transaction(function () use ($order_data) {
                            $res = self::where('id', $order_data['id'])->update(['status' => 10, 'close_at' => date('Y-m-d H:i:s')]);
                            $add_order_log = OrderLog::create(['order_id' => $order_data['id'], 'action' => '系统取消订单', 'note' => '系统取消订单']);
                            if ($order_data['coupons_id']) {
                                $back_coupons = CouponsDetail::backCoupons($order_data['coupons_id']);
                            }

                            $ios_predefined = [
                                'alert'       => ['title' => '订单超时取消', 'body' => '因您在规定时间内未完成支付，订单已自动取消。'],
                                'description' => '因您在规定时间内未完成支付，订单已自动取消。',
                                'alias_type'  => 'ios',
                                'badge'       => 0,
                                'sound'       => 'chime',
                            ];
                            $android_predefined = [
                                'title'       => '订单超时取消',
                                'description' => '因您在规定时间内未完成支付，订单已自动取消。',
                                'alias_type'  => 'android',
                                'text'        => '订单超时取消',
                                'ticker'      => 'title',
                                'after_open'  => 'go_app',
                            ];

                            PushService::iosSendCustomizedcast($order_data['m_id'], 'ios', $ios_predefined);
                            PushService::androidSendCustomizedcast($order_data['m_id'], 'android', $android_predefined);
                        });

                        return true;
                    } catch (\Exception $e) {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }

        /**
         * 系统自动确认订单
         * @param $order_id
         */
        public static function orderAutoConfirm($order_id)
        {
            if (!empty($order_id)) {
                $order_data = self::where('id', $order_id)->first();
                if ($order_data && $order_data['status'] == '2') {
                    try {
                        DB::transaction(function () use ($order_id) {
                            $res = self::where('id', $order_id)->update(['status' => 3, 'done_at' => date('Y-m-d H:i:s')]);
                            $add_order_log = OrderLog::create(['order_id' => $order_id, 'action' => '系统自动确认', 'note' => '系统自动确认']);
                        });

                        return true;
                    } catch (\Exception $e) {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }

    }