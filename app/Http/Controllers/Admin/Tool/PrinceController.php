<?php

namespace App\Http\Controllers\Admin\Tool;

use App\Models\YuVideo;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PrinceController extends Controller
{
    public function lists()
    {
        return view('admin.tool.prince.lists');
    }

    /**
     * 列表ajax数据
     */
    public function listsAjax(Request $request)
    {
        $limit = $request->input('limit',10);
        $keyword = $request->input('keyword');

        $where = [];
        if($keyword){
            $where[] = ['title','like','%'.$keyword.'%'];
        }

        //1-于文红视频,2-大咖说',3、小王子专版
        $where[] = ['type','=',3];

        $result = YuVideo::select('id','title', 'image', 'status','position','created_at')
                ->where($where)
                ->orderBy('id','desc')
                ->paginate($limit)->toArray();
        if(!$result['data']){
            return res_error('数据为空');
        }

        return res_success($result['data'], $result['total']);
    }

    /**
     * 添加编辑
     */
    public function edit(Request $request)
    {
        if($request->isMethod('post')){
            //验证规则
            $validator = Validator::make($request->all(), [
                'title'         => 'required',
                'image'     => 'required',
                'lang'          => 'numeric',
                'position'      => 'numeric',
                'url'           => 'url',
            ], [
                 'title.required'           => '标题不能为空',
                 'image.required'       => '图片不能为空',
                 'lang.numeric'             => '请选择正确的语言',
                 'position.numeric'         => '排序只能是数字',
                 'url.url'                  => '请填写正确的视频地址',
            ]);

            $error = $validator->errors()->all();
            if($error){
                return res_error(current($error));
            }

            $save_data = [];
            foreach($request->only(['title','image','lang','position','url']) as $key=>$val){
                $save_data[$key] = ($val || $val == 0) ? $val : null;
            }
            $save_data['type'] = 3;

            if($request->id){
                $res = YuVideo::where('id',$request->id)->update($save_data);
            }else{
                $res = YuVideo::create($save_data);
            }

            if($res){
                return res_success();
            }else{
                return res_error('保存失败');
            }
        }else{
            $item = [];
            $lang = config('app.config_lang');
            if($request->id){
                $item = YuVideo::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }

            return view('admin.tool.prince.edit',['item'=>$item,'lang'=>$lang]);
        }
    }

    /**
     * 修改状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request) {
        $id = $request->id;
        $ids = [];
        if (is_array($id)) {
            foreach ($id as $val) {
                $ids[] = (int)$val;
            }
        } else {
            $ids = array((int)$id);
        }
        $status = (int)$request->status;
        if ($ids && isset($status)) {
            $res = YuVideo::whereIn('id', $ids)->update(['status' => $status]);
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
    public function position(Request $request) {
        $id = (int)$request->id;
        $position = (int)$request->position;
        if ($id && isset($position)) {
            $res = YuVideo::where('id', $id)->update(['position' => $position]);
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

        //1-于文红视频,2-大咖说',3、小王子专版
        $res = YuVideo::whereIn('id', $ids)->where('type',3)->delete();
        if ($res) {
            return res_success();
        } else {
            return res_error('删除失败');
        }
    }

}
