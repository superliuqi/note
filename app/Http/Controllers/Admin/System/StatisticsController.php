<?php

    namespace App\Http\Controllers\Admin\System;

    use App\Models\AppDownLog;
    use App\Models\BalanceDetail;
    use App\Models\MeibiDetail;
    use App\Models\Member;
    use App\Service\BaiduService;
    use Illuminate\Support\Facades\DB;
    use Validator;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    class StatisticsController extends Controller
    {
        /**
         * 百度统计列表
         */
        public function lists(Request $request)
        {
            return view('admin.system.statistics.lists');
        }

        /**
         * 列表ajax数据
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function listsAjax(Request $request)
        {

            $data = '';
            $where = [];
            $os = $request->input('os');
            $start_time = $request->input('start_time');
            $end_time = $request->input('end_time');
            if ($start_time && $end_time) {
                $this_month = [substr($start_time, 0, - 9), substr($end_time, 0, - 9)];
            } else {
                //$this_months = getMonth(date('Y-m-d', strtotime('0 week')));//获取本月日期
                $this_month=getLeftDay();//获取当前日期前30天
            }

            if ($os) $where[] = ['os', $os];


            $download = AppDownLog::where(array_merge_recursive($where, ['is_click' => 1]))
                ->whereBetween('created_at', $this_month)
                ->groupBy('date')
                ->get([DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as value')]);
            if ($download) {
                $download = $download->toArray();
                $download = array_column($download, null, 'date');
            }

            $open = AppDownLog::where(array_merge_recursive($where, ['is_open' => 1]))
                ->whereBetween('open_at', $this_month)
                ->groupBy('date')
                ->get([DB::raw('DATE(open_at) as date'), DB::raw('COUNT(*) as value')]);
            if ($open) {
                $open = $open->toArray();
                $open = array_column($open, null, 'date');
            }

            $reg = AppDownLog::where(array_merge_recursive($where, ['is_reg' => 1]))
                ->whereBetween('reg_at', $this_month)
                ->groupBy('date')
                ->get([DB::raw('DATE(reg_at) as date'), DB::raw('COUNT(*) as value')]);
            if ($reg) {
                $reg = $reg->toArray();
                $reg = array_column($reg, null, 'date');
            }


            $title = 'Day,下载量,打开量,注册量' . "\r\n";

            foreach (getDateFromRange($this_month) as $key => $value) {
                $ymd = date('m/d/y', strtotime($value));
                $downcount = '0';
                $opencount = '0';
                $regcount = '0';
                if (isset($download[$value]['value'])) {
                    $downcount = $download[$value]['value'];
                }
                if (isset($open[$value]['value'])) {
                    $opencount = $open[$value]['value'];
                }
                if (isset($reg[$value]['value'])) {
                    $regcount = $reg[$value]['value'];
                }
                $data .= $ymd . ',' . $downcount . ',' . $opencount . ',' . $regcount . "\r\n";

            }
            $datas = BaiduService::dataCsv($title, $data);

            return (json_encode($datas));
        }


        /**
         * 统计美币列表
         */
        public function meibi_lists(Request $request)
        {
            return view('admin.system.statistics.meibi_lists');
        }

        /**
         * 列表ajax数据
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function meibi_listsAjax(Request $request)
        {

            $data = '';
            $where = [];
            $wheres = [];
            $username = $request->input('username');
            $start_time = $request->input('start_time');
            $end_time = $request->input('end_time');
            if ($start_time && $end_time) {
                $this_month = [substr($start_time, 0, - 9), substr($end_time, 0, - 9)];
            } else {
                $this_month = getWeek(time());//获取本周日期
            }


            if ($username) {

                $where[] = ['username', '=', $username];
                $member_id = Member::where($where)
                    ->value('id');
                $wheres[] = ['m_id', '=', $member_id];
            }

            //美币增加
            $meibi_add = MeibiDetail::where(array_merge_recursive($wheres, ['type' => 1]))
                ->whereBetween('created_at', $this_month)
                ->groupBy('date')
                ->get([DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as value')]);


            if ($meibi_add) {
                $meibi_add = $meibi_add->toArray();
                $meibi_add = array_column($meibi_add, null, 'date');
            }


            //美币减少
            $meibi_reduce = MeibiDetail::where(array_merge_recursive($wheres, ['type' => 2]))
                ->whereBetween('created_at', $this_month)
                ->groupBy('date')
                ->get([DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as value')]);

            if ($meibi_reduce) {
                $meibi_reduce = $meibi_reduce->toArray();
                $meibi_reduce = array_column($meibi_reduce, null, 'date');
            }

            $title = 'Day,增加总金额,减少总金额' . "\r\n";

            foreach (getDateFromRange($this_month) as $key => $value) {

                $add_amount = 0;
                $reduce_amount = 0;

                $ymd = date('m/d/y', strtotime($value));

                if (isset($meibi_add[$value]['value'])) {
                    $add_amount = $meibi_add[$value]['value'];
                }

                if (isset($meibi_reduce[$value]['value'])) {
                    $reduce_amount = $meibi_reduce[$value]['value'];
                }

                $data .= $ymd . ',' . $add_amount . ',' . $reduce_amount . "\r\n";

            }
            $datas = BaiduService::dataCsv($title, $data);

            return (json_encode($datas));
        }


        /**
         * 统计余额列表
         */
        public function balance_lists(Request $request)
        {
            return view('admin.system.statistics.balance_lists');
        }

        /**
         * 列表ajax数据
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function balance_listsAjax(Request $request)
        {

            $data = '';
            $where = [];
            $wheres = [];
            $username = $request->input('username');
            $start_time = $request->input('start_time');
            $end_time = $request->input('end_time');
            if ($start_time && $end_time) {
                $this_month = [substr($start_time, 0, - 9), substr($end_time, 0, - 9)];
            } else {
                $this_month = getWeek(time());//获取本周日期
            }


            if ($username) {

                $where[] = ['username', '=', $username];
                $member_id = Member::where($where)
                    ->value('id');
                $wheres[] = ['m_id', '=', $member_id];
            }

            //余额增加
            $balance_add = BalanceDetail::where(array_merge_recursive($wheres, ['type' => 1]))
                ->whereBetween('created_at', $this_month)
                ->groupBy('date')
                ->get([DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as value')]);


            if ($balance_add) {
                $balance_add = $balance_add->toArray();
                $balance_add = array_column($balance_add, null, 'date');
            }


            //余额减少
            $balance_reduce = BalanceDetail::where(array_merge_recursive($wheres, ['type' => 2]))
                ->whereBetween('created_at', $this_month)
                ->groupBy('date')
                ->get([DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as value')]);

            if ($balance_reduce) {
                $balance_reduce = $balance_reduce->toArray();
                $balance_reduce = array_column($balance_reduce, null, 'date');
            }

            $title = 'Day,增加总金额,减少总金额' . "\r\n";

            foreach (getDateFromRange($this_month) as $key => $value) {

                $add_amount = 0;
                $reduce_amount = 0;

                $ymd = date('m/d/y', strtotime($value));

                if (isset($balance_add[$value]['value'])) {
                    $add_amount = $balance_add[$value]['value'];
                }

                if (isset($balance_reduce[$value]['value'])) {
                    $reduce_amount = $balance_reduce[$value]['value'];
                }

                $data .= $ymd . ',' . $add_amount . ',' . $reduce_amount . "\r\n";

            }
            $datas = BaiduService::dataCsv($title, $data);

            return (json_encode($datas));
        }


    }
