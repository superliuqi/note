<?php

    namespace App\Http\Controllers\Admin\Tool;
    use App\Models\Friend;
    use App\Models\FriendImage;
    use App\Models\Member;
    use Validator;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    class FriendController extends Controller
    {
        public function lists()
        {
            return view('admin.tool.friend.lists');
        }



        /**
         * 列表ajax数据
         */
        public function listsAjax(Request $request)
        {
            $limit = $request->input('limit', 10);
            $nick_name = $request->input('nick_name');
            $username = $request->input('username');
            $where = [];


            if(isset($nick_name) && $nick_name){
                $m_id = Member::where('nick_name',$nick_name)->value('id');
                if($m_id){
                    $where[] = ['m_id','=',$m_id];
                }
            }

            if(isset($username) && $username){
                $m_id = Member::where('username',$username)->value('id');
                if($m_id){
                    $where[] = ['m_id','=',$m_id];
                }
            }

            $result = Friend::select('id', 'm_id', 'subject','type','video', 'status', 'created_at')
                ->where($where)
                ->orderBy('id', 'desc')
                ->paginate($limit)->toArray();

            if (!$result['data']) {
                return res_error('数据为空');
            }

            $m_ids = $diary_ids = [];
            foreach ($result['data'] as $key => $value) {
                $m_ids[] = $value['m_id'];
                $diary_ids[] = $value['id'];
            }
            $m_ids = array_unique($m_ids);
            $diary_ids = array_unique($diary_ids);
            //用户信息
            $member_info = Member::whereIn('id', $m_ids)->select('id', 'username', 'nick_name')->get()->toArray();
            $member_info = array_column($member_info, null, 'id');


            //朋友圈图片
            $img = FriendImage::whereIn('friend_id',$diary_ids)->select('url','friend_id')->get()->toArray();
            $imgs = [];
            if($img){
                foreach($img as $ik=>$iv){
                    $imgs[$iv['friend_id']][]= $iv['url'];
                }
            }
            foreach ($result['data'] as $k => $v) {
                $result['data'][$k]['username']     = $member_info[$v['m_id']]['username'];
                $result['data'][$k]['nick_name']    = $member_info[$v['m_id']]['nick_name'];
                $result['data'][$k]['image']        = isset($imgs[$v['id']]) ? $imgs[$v['id']] : '';
            }

            return res_success($result['data'], $result['total']);
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
                $item = Friend::find($request->id)->toArray();
                if (!$item) {
                    return res_error('数据错误');
                }
            }

            return view('admin.tool.friend.play', ['item' => $item]);
        }


        /**
         * 修改状态
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function status(Request $request)
        {
            $id = $request->id;
            if (is_array($request->id)) {
                foreach ($id as $val) {
                    $ids[] = (int)$val;
                }
            } else {
                $ids = [(int)$id];
            }
            $status = (int)$request->status;
            if ($ids && isset($status)) {
                $res = Friend::whereIn('id', $ids)->update(['status' => $status]);
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

            try{
                $res = DB::transaction(function()use($request,$ids){
                    $del_res = Friend::whereIn('id', $ids)->delete();
                    $del = FriendImage::whereIn('friend_id',$ids)->delete();
                    return $del;
                });
            }catch(\Exception $e){
                $res = false;
            }

            if ($res) {
                return res_success();
            } else {
                return res_error('删除失败');
            }
        }
    }
