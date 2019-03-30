<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Meibi extends Model
{
    protected $table = 'meibi';
    protected $guarded = ['id'];

    /**
     * 修改美币账户并记录详情
     * @param int $m_id 用户id
     * @param $amount 金额
     * @param int $event 类型
     * @param string $detail_no 单号
     * @param string $note 备注
     * @return bool|mixed
     */
    public static function updateAmount($m_id, $amount, $event, $detail_no = '', $note = '') {
        $return = array(
            'status' => 1,
            'message' => __('api.fail')
        );
        $m_id = (int)$m_id;
        $event = (int)$event;
        if (!$m_id || !$amount || !$event) {
            $return['message'] = __('api.missing_params');
            return $return;
        }

        if (!isset(MeibiDetail::EVENT_DESC[$event])) {
            $return['message'] = __('api.balance_event_error');
            return $return;
        }
        //变动详情
        $detail = array(
            'm_id' => $m_id,
            'type' => $amount >= 0 ? MeibiDetail::TYPE_INCR : MeibiDetail::TYPE_RECR,
            'event' => $event,
            'amount' => abs($amount),
            'detail_no' => $detail_no,
            'note' => $note
        );
        $res_data = self::where('m_id', $m_id)->first();
        
        //减少时判断余额是否足够
        if ($amount < 0 && (!isset($res_data['amount']) || ($res_data['amount'] + $amount) < 0)) {
            $return['message'] = __('api.balance_no_enough');
            return $return;
        }
        try {
            $res = DB::transaction(function () use ($res_data, $m_id, $amount, $detail) {
                //数据存在的时候直接修改
                if ($res_data) {
                    $where[] = ['m_id', $m_id];
                    //减少的时候加上条件
                    if ($amount < 0) {
                        $where[] = ['amount', '>=', $amount];
                    }
                    $res = self::where($where)->increment('amount', $amount);
                } else {
                    if ($amount >= 0) {
                        $result = self::create(['m_id' => $m_id, 'amount' => $amount]);
                        $res = $result->id;
                    }
                }
                MeibiDetail::create($detail);
                return $res;
            });
        } catch (\Exception $e) {
            $res = false;
        }
        if ($res) {
            $return['status'] = 0;
            $return['message'] = '';
            return $return;
        }
        return $return;
    }

    /**
     * 美币兑换成人民币
     * @param int $m_id 用户id
     * @param $amount 美币金额
     * @param int $event 类型
     * @param string $detail_no 单号
     * @param string $note 备注
     * @return bool|mixed
     */
    public static function exchange($m_id, $amount, $event, $detail_no = '', $note = '') {
        $return = array(
            'status' => 1,
            'message' => __('api.fail')
        );
        $m_id = (int)$m_id;
        $event = (int)$event;
        if (!$m_id || !$amount || !$event) {
            $return['message'] = __('api.missing_params');
            return $return;
        }

        if ($amount >= 0) {
            $return['message'] = __('api.invalid_params');
            return $return;
        }

        if (!isset(MeibiDetail::EVENT_DESC[$event])) {
            $return['message'] = __('api.balance_event_error');
            return $return;
        }
        //变动详情
        $detail = array(
            'm_id' => $m_id,
            'type' => $amount >= 0 ? MeibiDetail::TYPE_INCR : MeibiDetail::TYPE_RECR,
            'event' => $event,
            'amount' => abs($amount),
            'detail_no' => $detail_no,
            'note' => $note
        );
        $res_data = self::where('m_id', $m_id)->first();
        //减少时判断余额是否足够
        if ($amount < 0 && (!isset($res_data['amount']) || ($res_data['amount'] + $amount) < 0)) {
            $return['message'] = __('api.balance_no_enough');
            return $return;
        }
        try {
            $res = DB::transaction(function () use ($res_data, $m_id, $amount, $detail, $note) {
                //数据存在的时候直接修改
                if ($res_data) {
                    $where[] = ['m_id', $m_id];
                    //减少的时候加上条件
                    if ($amount < 0) {
                        $where[] = ['amount', '>=', abs($amount)];
                    }
                    $res = self::where($where)->increment('amount', $amount);
                    MeibiDetail::create($detail);
                    $rmb = abs($amount) * config('app.meibi_to_balance');
                    $add_balance = Balance::updateAmount($m_id, $rmb, BalanceDetail::EVENT_MEIBI_EXCHANGE, '', $note);
                } else {
                    $res = false;
                    $return['message'] = __('api.balance_no_enough');
                }
                return $res;
            });
        } catch (\Exception $e) {
            $res = false;
        }
        if ($res) {
            $return['status'] = 0;
            $return['message'] = '';
            return $return;
        }
        return $return;
    }
}
