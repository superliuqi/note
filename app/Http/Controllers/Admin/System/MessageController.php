<?php

namespace App\Http\Controllers\Admin\System;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    public function lists()
    {
        return view('admin.system.message.lists');
    }

    /**
     * ajax数据列表
     */
    public function listsAjax(Request $request)
    {
        $keyword = $request->input('keyword');
        $limit = $request->input('limit',10);

        $where = [];
        if($keyword){
            $where[] = ['desc', 'like', '%' . $keyword . '%'];
        }

        $result = Message::where($where)
                ->select('id','desc','created_at')
                ->orderBy('id','desc')
                ->paginate($limit)
                ->toArray();

        if(!$result['data']){
            return res_error('数据为空');
        }

        return res_success($result['data'],$result['total']);
    }

    /**
     * 数据删除
     */
    public function delete(Request $request)
    {
        $id = $request->id;
        $ids = [];
        if(is_array($id)){
            foreach($id as $val){
                $ids[] = (int)$val;
            }
        }else{
            $ids = array((int)$id);
        }

        if(!$ids){
            return res_error('参数有误');
        }

        $res = Message::whereIn('id',$ids)->delete();

        if($res){
            return res_success();
        }else{
            return res_error('删除失败');
        }
    }
}
