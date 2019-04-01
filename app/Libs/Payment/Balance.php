<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/25
 * Time: 上午10:28
 */

namespace App\Libs\Payment;

use App\Models\BalanceDetail;
use App\Models\Member;
use App\Models\Trade;
use App\Service\TradeService;
use Illuminate\Support\Facades\Hash;

/**
 * 余额支付
 * Class Balance
 * @package App\Libs\Payment
 */
class Balance
{

    /**
     * 开始支付
     * @param $trade_data
     * @return array
     * @throws WxPayException
     */
    public function payData($trade_data) {
        if (!$trade_data['m_id'] || !$trade_data['trade_no'] || !$trade_data['amount'] || !$trade_data['type']) {
            api_error(__('api.missing_params'));
        }
        //钱包充值不能使用钱包支付
        if (!in_array($trade_data['type'], [Trade::TYPE_ORDER, Trade::TYPE_MEIBI])) {
            api_error(__('api.pay_type_error'));
        }
        $event = '';
        if ($trade_data['type'] == Trade::TYPE_ORDER) {
            $event = BalanceDetail::EVENT_ORDER_PAY;
        } elseif ($trade_data['type'] == Trade::TYPE_MEIBI) {
            $event = BalanceDetail::EVENT_MEIBI_BUY;
        }
        //判断支付密码
        $pay_password = request()->post('pay_password');
        if (!$pay_password) {
            api_error(__('api.missing_params'));
        }
        $member_data = Member::find($trade_data['m_id']);
        if (empty($member_data['pay_password'])) {
            api_error(__('api.member_pay_password_notset'));
        }
        if (!Hash::check($pay_password, $member_data['pay_password'])) {
            api_error(__('api.member_pay_password_error'));
        }
        //开始扣除余额
        $note = '支付交易单' . $trade_data['trade_no'];
        $res = \App\Models\Balance::updateAmount($trade_data['m_id'], -$trade_data['amount'], $event, $trade_data['trade_no'], $note);
        if ($res['status'] == 0) {
            //支付成功修改交易单状态
            $return = array(
                'trade_no' => $trade_data['trade_no'],
                'pay_total' => $trade_data['amount'],
                'payment_no' => $trade_data['trade_no'],
                'payment_id' => 5
            );
            $trade_service = new TradeService();
            $res = $trade_service->updateStatus($return);
            if ($res) {
                return true;
            } else {
                api_error(__('api.fail'));
            }
        } else {
            api_error($res['message']);
        }
    }

    /**
     * 支付成功
     * @return string
     */
    public function success() {
        return 'success';
    }

    /**
     * 支付失败
     * @return string
     */
    public function fail() {
        return 'fail';
    }
}