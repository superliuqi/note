<?php

    namespace App\Http\Controllers\Admin\Tool;
    use App\Models\Diary;
    use App\Models\DiaryImage;
    use App\Models\DiaryContent;
    use App\Models\DiaryDetail;
    use App\Models\Member;
    use Validator;
    use Illuminate\Support\Facades\DB;
    use App\Models\DoctorSay;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    class DiaryController extends Controller
    {
        public function lists()
        {
            return view('admin.tool.diary.lists');
        }



        /**
         * 列表ajax数据
         */
        public function listsAjax(Request $request)
        {
            $limit = $request->input('limit', 10);
            $nick_name = $request->input('nick_name');
            $username = $request->input('username');
            $is_rem = $request->input('is_rem');
            $where = [];

            if(isset($is_rem)){
                $where[] = ['is_rem','=',$is_rem];
            }

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

            $result = Diary::select('id', 'm_id', 'is_rem', 'status', 'created_at')
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

            //日记详情  点赞 评论等
            $diary_details = DiaryDetail::whereIn('diary_id', $diary_ids)->select('diary_id', 'comment_num', 'support_num', 'report_num')->get()->toArray();
            $diary_details = array_column($diary_details, null, 'diary_id');

            //日记内容
            $diary_contents = DiaryContent::whereIn('diary_id', $diary_ids)->pluck('content','diary_id');

            //日记图片
            $img = DiaryImage::whereIn('diary_id',$diary_ids)->select('image','diary_id')->get()->toArray();
            $imgs = [];
            if($img){
                foreach($img as $ik=>$iv){
                    $imgs[$iv['diary_id']][]= $iv['image'];
                }
            }
            foreach ($result['data'] as $k => $v) {
                $result['data'][$k]['username']     = $member_info[$v['m_id']]['username'];
                $result['data'][$k]['nick_name']    = $member_info[$v['m_id']]['nick_name'];
                $result['data'][$k]['comment_num']  = isset($diary_details[$v['id']]['comment_num']) ? $diary_details[$v['id']]['comment_num'] : 0;
                $result['data'][$k]['support_num']  = isset($diary_details[$v['id']]['support_num']) ? $diary_details[$v['id']]['support_num'] : 0;
                $result['data'][$k]['report_num']   = isset($diary_details[$v['id']]['report_num']) ? $diary_details[$v['id']]['report_num'] : 0;
                $result['data'][$k]['image']        = isset($imgs[$v['id']]) ? $imgs[$v['id']] : '';
                $result['data'][$k]['content']      = isset($diary_contents[$v['id']]) ? $diary_contents[$v['id']] : '';
            }

            return res_success($result['data'], $result['total']);
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
                $res = Diary::whereIn('id', $ids)->update(['status' => $status]);
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
         * 推荐
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function rem(Request $request)
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
                $res = Diary::whereIn('id', $ids)->update(['is_rem' => $is_rem]);
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
                    $del_res = Diary::whereIn('id', $ids)->delete();
                    $del = DiaryImage::whereIn('diary_id',$ids)->delete();
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
