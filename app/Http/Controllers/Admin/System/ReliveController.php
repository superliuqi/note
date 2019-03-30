<?php

    namespace App\Http\Controllers\Admin\System;

    use App\Models\Live;
    use App\Models\LiveLinkMicrophone;
    use App\Models\LiveRobotLog;
    use App\Models\Member;
    use Illuminate\Database\Eloquent\Model;
    use Validator;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Redis;

    class ReliveController extends Controller
    {
        /**
         * 列表
         */
        public function lists(Request $request)
        {
            return view('admin.system.relive.lists');
        }

        /**
         * 列表ajax数据
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function listsAjax(Request $request)
        {
            $limit = $request->input('limit', 10);
            $keyword = $request->input('keyword');
            $is_rem = $request->input('is_rem');
            $username = $request->input('username');
            $m_id = $request->input('m_id');

            //搜索
            $where = [];
            //$where[] = ['status', '=', Live::STATUS_OFF];
            if ($keyword) {
                $where[] = ['title', 'like', '%' . $keyword . '%'];
            }
            if (is_numeric($is_rem) === true) {
                $where[] = ['is_rem', '=', $is_rem];
            }
            if ($username) {
                $mid = Member::where('username', $username)->value('id');
                $where[] = ['m_id', '=', $mid];
            }
            if ($m_id) {
                $where[] = ['m_id', '=', $m_id];
            }
            $result = Live::select('id', 'title', 'status', 'headimg','password', 'nick_name', 'file_id', 'created_at', 'video_url', 'video_length', 'end_at', 'position', 'is_rem', 'updated_at', 'all_number', 'robot_numbet')
                ->where($where)
                ->orderBy('id', 'desc')
                ->paginate($limit)->toArray();
            if (!$result['data']) {
                return res_error('数据为空');
            }


            $live_ids = [];
            foreach ($result['data'] as $value) {
                $live_ids[] = $value['id'];
            }

            if ($live_ids) {
                $link = LiveLinkMicrophone::whereIn('live_id', $live_ids)->where('status', 1)
                    ->pluck('status', 'live_id');

               // $live_robot_log = LiveRobotLog::whereIn('live_id', $live_ids)
               //     ->count(); //实时机器人数量
            }

            $data_list = [];
            foreach ($result['data'] as $key => $value) {
                $_item = $value;
                $real_num = Redis::llen('live_user:' . $value['id']) - LiveRobotLog::where('live_id', $value['id'])->count();
                $_item['real_time_number'] = isset($real_num) ? $real_num : 0;
                $_item['video_length'] = isset($value['video_length']) ? $this->get_hour($value['video_length']) : '00:00:00';
                $_item['link'] = isset($link[$value['id']]) ? $link[$value['id']] : 0;
                $data_list[] = $_item;
            }

            return res_success($data_list, $result['total']);
        }

        public function get_hour($seconds)
        {
            if ($seconds > 3600){
                $hours = intval($seconds/3600);
                $minutes = $seconds % 3600;
                $time = $hours.":".gmstrftime('%M:%S', $minutes);
            }else{
                $time = gmstrftime('%H:%M:%S', $seconds);
            }
            return $time;
        }


        /**
         * 连麦列表
         */
        public function linkLists(Request $request)
        {
            $live_id = $request->live_id;

            return view('admin.system.link.lists', ['live_id' => $live_id]);
        }

        /**
         * 连麦列表ajax数据
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function linkListsAjax(Request $request)
        {
            $limit = $request->input('limit', 10);

            $live_id = (int)$request->live_id;
            $where = [];
            $where[] = ['live_id', $live_id];
            $result = LiveLinkMicrophone::select('id', 'live_id', 'live_m_id', 'link_m_id', 'status', 'end_at', 'created_at')
                ->where($where)
                ->orderBy('id', 'desc')
                ->paginate($limit)->toArray();
            if (!$result['data']) {
                return res_error('数据为空');
            }


            $live_ids = [];
            $link_user_ids = [];
            $live = [];
            foreach ($result['data'] as $value) {
                $live_ids[] = $value['live_id'];
            }

            foreach ($result['data'] as $value) {
                $link_user_ids[] = $value['link_m_id'];
            }

            if ($live_ids) {
                $live = Live::whereIn('id', $live_ids)->select('id', 'title', 'headimg', 'nick_name')->get();
            }
            if ($live) {
                $live = $live->toArray();
                $live = array_column($live, null, 'id');
            }

            if ($link_user_ids) {
                $link = Member::whereIn('id', $link_user_ids)->pluck('nick_name', 'id');
            }

            $data_list = [];
            foreach ($result['data'] as $key => $value) {
                $_item = $value;
                $_item['title'] = isset($live[$value['live_id']]['title']) ? $live[$value['live_id']]['title'] : '';
                $_item['headimg'] = isset($live[$value['live_id']]['headimg']) ? $live[$value['live_id']]['headimg'] : '';
                $_item['nick_name'] = isset($live[$value['live_id']]['nick_name']) ? $live[$value['live_id']]['nick_name'] : '';
                $_item['link_nick_name'] = isset($link[$value['link_m_id']]) ? $link[$value['link_m_id']] : '';
                $data_list[] = $_item;
            }

            return res_success($data_list, $result['total']);
        }


        /**
         * 播放视频
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function play_url(Request $request)
        {
            //$file_id = $request->file_id;//fild_id
            $item = [];
            if ($request->id) {
                $item = Live::find($request->id)->toArray();
                if (!$item) {
                    return res_error('数据错误');
                }
            }

            return view('admin.system.relive.play', ['item' => $item]);
        }


        /**
         * 推荐
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function isRem(Request $request)
        {
            $id = $request->id;
            if (is_array($request->id)) {
                foreach ($id as $val) {
                    $ids[] = (int)$val;
                }
            } else {
                $ids = [(int)$id];
            }
            $is_rem = (int)$request->is_rem;
            if ($ids && isset($is_rem)) {
                $res = Live::whereIn('id', $ids)->update(['is_rem' => $is_rem]);
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
        public function position(Request $request)
        {
            $id = (int)$request->id;
            $position = (int)$request->field;
            if ($id && isset($position)) {
                $res = Live::where('id', $id)->update(['position' => $position]);
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
         * 修改密码
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function password(Request $request)
        {
            $id = (int)$request->id;
            $password = (int)$request->value;
            if ($id && isset($password)) {
                $res = Live::where('id', $id)->update(['password' => $password]);
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
