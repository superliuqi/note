<?php

    namespace App\Http\Controllers\Admin\member;

    use App\Models\Meibi;
    use App\Models\MeibiDetail;
    use App\Models\Member;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Validator;
    use Illuminate\Support\Facades\DB;

    class MeibiController extends Controller
    {
        public function lists()
        {
            return view('admin.member.meibi.lists');
        }

        /**
         * 美币列表ajax数据
         */
        public function listsAjax(Request $request)
        {
            $keyword = $request->input('keyword');
            $limit = $request->input('limit');
            $event = $request->input('event');
            $where = [];
            $wheres = [];
            if ($keyword) {
                $where[] = ['username', '=', $keyword];
                $member_id = Member::where($where)
                    ->value('id');
                $wheres[] = ['m_id', '=', $member_id];
            }

            if ($event) {
                $wheres[] = ['event', '=', $event];
            }
            
            $result = MeibiDetail::where($wheres)
                ->select('id', 'm_id', 'type', 'amount', 'note', 'created_at', 'event')
                ->orderBy('id', 'desc')
                ->paginate($limit);

            if ($result->isEmpty()) {
                return res_error('暂无明细');
            } else {
                $res = $result->toArray();

                $m_ids = [];
                foreach ($res['data'] as $val) {
                    $m_ids[] = $val['m_id'];
                }
                $username = Member::whereIn('id', $m_ids)->pluck('username', 'id');
                foreach ($res['data'] as $k => $v) {
                    $res['data'][$k]['event'] = MeibiDetail::EVENT_DESC[$v['event']];
                    $res['data'][$k]['username'] = $username[$v['m_id']];
                }
            }

            return res_success($res['data'], $res['total']);
        }


    }
