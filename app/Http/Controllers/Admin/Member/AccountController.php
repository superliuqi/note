<?php

    namespace App\Http\Controllers\Admin\member;

    use App\Models\Balance;
    use App\Models\BalanceDetail;
    use App\Models\Meibi;
    use App\Models\MeibiDetail;
    use App\Models\Member;
    use App\Models\Point;
    use App\Models\PointDetail;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Validator;
    use Illuminate\Support\Facades\DB;

    class AccountController extends Controller
    {
        public function lists()
        {
            return view('admin.member.account.lists');
        }

        /**
         * 列表ajax数据
         */
        public function listsAjax(Request $request)
        {
            $keyword = $request->input('keyword');
            $limit = $request->input('limit');

            $where = [];

            if ($keyword) {
                $where[] = ['username', 'like', '%' . $keyword . '%'];
            }

            $result = Member::where($where)
                ->select('id', 'username', 'nick_name', 'headimg')
                ->orderBy('id', 'desc')
                ->paginate($limit)
                ->toArray();

            if (!$result['data']) {
                return res_error('数据为空');
            }

            $m_ids = [];
            foreach ($result['data'] as $val) {
                $m_ids[] = $val['id'];
            }

            $balance = Balance::whereIn('m_id', $m_ids)->pluck('amount', 'm_id');
            $meibi = Meibi::whereIn('m_id', $m_ids)->pluck('amount', 'm_id');
            $point = Point::whereIn('m_id', $m_ids)->pluck('amount', 'm_id');
            foreach ($result['data'] as $k => $v) {
                $result['data'][$k]['balance_amount'] = isset($balance[$v['id']]) ? $balance[$v['id']] : '0.00';
                $result['data'][$k]['meibi_amount'] = isset($meibi[$v['id']]) ? $meibi[$v['id']] : '0.00';
                $result['data'][$k]['point_amount'] = isset($point[$v['id']]) ? $point[$v['id']] : '0.00';
            }

            return res_success($result['data'], $result['total']);
        }


        /**
         * 资金 明细列表
         */
        public function balanceDetail(Request $request)
        {
            $m_id = $request->get('m_id', 0);

            return view('admin.member.account.balance_detail', ['m_id' => $m_id]);
        }

        /**
         * 美币 明细列表
         */
        public function meibiDetail(Request $request)
        {
            $m_id = $request->get('m_id', 0);

            return view('admin.member.account.meibi_detail', ['m_id' => $m_id]);
        }

        /**
         * 积分 明细列表
         */
        public function pointDetail(Request $request)
        {
            $m_id = $request->get('m_id', 0);

            return view('admin.member.account.point_detail', ['m_id' => $m_id]);
        }


        /**
         * 资金明细ajax数据
         */
        public function balanceAjax(Request $request)
        {
            $limit = $request->input('limit');
            $m_id = (int)$request->input('m_id', 0);
            $event = (int)$request->input('event');

            $where[] = ['m_id', '=', $m_id];

            if ($event != '') {
                $where[] = ['event', '=', $event];
            }

            $result = BalanceDetail::where($where)
                ->select('id', 'm_id', 'type', 'amount', 'note', 'created_at', 'event')
                ->orderBy('id', 'desc')
                ->paginate($limit);

            if ($result->isEmpty()) {
                return res_error('暂无明细');
            } else {
                $res = $result->toArray();
                foreach ($res['data'] as $k => $v) {
                    $res['data'][$k]['event'] = BalanceDetail::EVENT_DESC[$v['event']];
                }
            }

            return res_success($res['data'], $res['total']);
        }

        /**
         * 美币明细ajax数据
         */
        public function meibiAjax(Request $request)
        {
            $limit = $request->input('limit');
            $m_id = (int)$request->input('m_id', 0);
            $event = (int)$request->input('event');

            $where[] = ['m_id', '=', $m_id];

            if ($event != '') {
                $where[] = ['event', '=', $event];
            }

            $result = MeibiDetail::where($where)
                ->select('id', 'm_id', 'type', 'amount', 'note', 'created_at', 'event')
                ->orderBy('id', 'desc')
                ->paginate($limit);

            if ($result->isEmpty()) {
                return res_error('暂无明细');
            } else {
                $res = $result->toArray();
                foreach ($res['data'] as $k => $v) {
                    $res['data'][$k]['event'] = MeibiDetail::EVENT_DESC[$v['event']];
                }
            }

            return res_success($res['data'], $res['total']);
        }

        /**
         * 美币明细ajax数据
         */
        public function pointAjax(Request $request)
        {
            $limit = $request->input('limit');
            $m_id = (int)$request->input('m_id', 0);
            $event = (int)$request->input('event');

            $where[] = ['m_id', '=', $m_id];

            if ($event != '') {
                $where[] = ['event', '=', $event];
            }

            $result = PointDetail::where($where)
                ->select('id', 'm_id', 'type', 'amount', 'note', 'created_at', 'event')
                ->orderBy('id', 'desc')
                ->paginate($limit);

            if ($result->isEmpty()) {
                return res_error('暂无明细');
            } else {
                $res = $result->toArray();
                foreach ($res['data'] as $k => $v) {
                    $res['data'][$k]['event'] = PointDetail::EVENT_DESC[$v['event']];
                }
            }

            return res_success($res['data'], $res['total']);
        }

        /**
         * 资金 系统充值
         */
        public function balanceAdd(Request $request)
        {
            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'm_id'   => 'required|numeric',
                    'amount' => 'numeric',
                ], [
                                                 'm_id.required'  => '用户id不能为空',
                                                 'm_id.numeric'   => '用户id不正确',
                                                 'amount.numeric' => '金额必须为数字',
                                             ]);

                $error = $validator->errors()->all();
                if ($error) {
                    return res_error(current($error));
                }
                $amount = $request->get('amount', 0);
                $m_id = (int)$request->get('m_id', 0);

                if (!$m_id) {
                    return res_error('用户id有误');
                }
                if ($amount <= 0) {
                    return res_error('金额有误');
                }
                $admin_user = \Auth::guard('admin')->user();
                $note = '管理员【' . $admin_user['username'] . '】充值资金【' . $amount . '】';
                $res = Balance::updateAmount($m_id, $amount, BalanceDetail::EVENT_SYSTEM_RECHARGE, '', $note);
                if ($res['status'] == '0') {
                    return res_success('添加成功');
                } else {
                    return res_error($res['message']);
                }
            }
        }

        /**
         * 美币 系统充值
         */
        public function meibiAdd(Request $request)
        {
            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'm_id'   => 'required|numeric',
                    'amount' => 'numeric',
                ], [
                                                 'm_id.required'  => '用户id不能为空',
                                                 'm_id.numeric'   => '用户id不正确',
                                                 'amount.numeric' => '金额必须为数字',
                                             ]);

                $error = $validator->errors()->all();
                if ($error) {
                    return res_error(current($error));
                }
                $amount = $request->get('amount', 0);
                $m_id = (int)$request->get('m_id', 0);

                if (!$m_id) {
                    return res_error('用户id有误');
                }
                if ($amount <= 0) {
                    return res_error('金额有误');
                }
                $admin_user = \Auth::guard('admin')->user();
                $note = '管理员【' . $admin_user['username'] . '】充值美币【' . $amount . '】';
                $res = Meibi::updateAmount($m_id, $amount, MeibiDetail::EVENT_SYSTEM_RECHARGE, '', $note);
                if ($res['status'] == '0') {
                    return res_success('添加成功');
                } else {
                    return res_error($res['message']);
                }
            }
        }

        /**
         * 积分 系统充值
         */
        public function pointAdd(Request $request)
        {
            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'm_id'   => 'required|numeric',
                    'amount' => 'numeric',
                ], [
                                                 'm_id.required'  => '用户id不能为空',
                                                 'm_id.numeric'   => '用户id不正确',
                                                 'amount.numeric' => '金额必须为数字',
                                             ]);

                $error = $validator->errors()->all();
                if ($error) {
                    return res_error(current($error));
                }
                $amount = $request->get('amount', 0);
                $m_id = (int)$request->get('m_id', 0);

                if (!$m_id) {
                    return res_error('用户id有误');
                }
                if ($amount <= 0) {
                    return res_error('金额有误');
                }
                $admin_user = \Auth::guard('admin')->user();
                $note = '管理员【' . $admin_user['username'] . '】充值积分【' . $amount . '】';
                $res = Point::updateAmount($m_id, $amount, PointDetail::EVENT_SYSTEM_RECHARGE, '', $note);
                if ($res['status'] == '0') {
                    return res_success('添加成功');
                } else {
                    return res_error($res['message']);
                }
            }
        }

        /**
         * 资金 系统扣除
         */
        public function balanceReduce(Request $request)
        {
            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'm_id'   => 'required|numeric',
                    'amount' => 'numeric',
                ], [
                                                 'm_id.required'  => '用户id不能为空',
                                                 'm_id.numeric'   => '用户id不正确',
                                                 'amount.numeric' => '金额必须为数字',
                                             ]);

                $error = $validator->errors()->all();
                if ($error) {
                    return res_error(current($error));
                }
                $amount = $request->get('amount', 0);
                $m_id = (int)$request->get('m_id', 0);

                if (!$m_id) {
                    return res_error('用户id有误');
                }
                if ($amount <= 0) {
                    return res_error('金额有误');
                }
                $admin_user = \Auth::guard('admin')->user();
                $note = '管理员【' . $admin_user['username'] . '】扣除资金【' . $amount . '】';
                $res = Balance::updateAmount($m_id, '-' . $amount, BalanceDetail::EVENT_SYSTEM_DEDUCT, '', $note);
                if ($res['status'] == '0') {
                    return res_success('扣除成功');
                } else {
                    return res_error($res['message']);
                }
            }
        }

        /**
         * 美币 系统扣除
         */
        public function meibiReduce(Request $request)
        {
            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'm_id'   => 'required|numeric',
                    'amount' => 'numeric',
                ], [
                                                 'm_id.required'  => '用户id不能为空',
                                                 'm_id.numeric'   => '用户id不正确',
                                                 'amount.numeric' => '金额必须为数字',
                                             ]);

                $error = $validator->errors()->all();
                if ($error) {
                    return res_error(current($error));
                }
                $amount = $request->get('amount', 0);
                $m_id = (int)$request->get('m_id', 0);

                if (!$m_id) {
                    return res_error('用户id有误');
                }
                if ($amount <= 0) {
                    return res_error('金额有误');
                }
                $admin_user = \Auth::guard('admin')->user();
                $note = '管理员【' . $admin_user['username'] . '】扣除美币【' . $amount . '】';
                $res = Meibi::updateAmount($m_id, '-' . $amount, MeibiDetail::EVENT_SYSTEM_DEDUCT, '', $note);
                if ($res['status'] == '0') {
                    return res_success('扣除成功');
                } else {
                    return res_error($res['message']);
                }
            }
        }

        /**
         * 积分 系统扣除
         */
        public function pointReduce(Request $request)
        {
            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'm_id'   => 'required|numeric',
                    'amount' => 'numeric',
                ], [
                                                 'm_id.required'  => '用户id不能为空',
                                                 'm_id.numeric'   => '用户id不正确',
                                                 'amount.numeric' => '金额必须为数字',
                                             ]);

                $error = $validator->errors()->all();
                if ($error) {
                    return res_error(current($error));
                }
                $amount = $request->get('amount', 0);
                $m_id = (int)$request->get('m_id', 0);

                if (!$m_id) {
                    return res_error('用户id有误');
                }
                if ($amount <= 0) {
                    return res_error('金额有误');
                }
                $admin_user = \Auth::guard('admin')->user();
                $note = '管理员【' . $admin_user['username'] . '】扣除积分【' . $amount . '】';
                $res = Point::updateAmount($m_id, '-' . $amount, PointDetail::EVENT_SYSTEM_DEDUCT, '', $note);
                if ($res['status'] == '0') {
                    return res_success('扣除成功');
                } else {
                    return res_error($res['message']);
                }
            }
        }
    }
