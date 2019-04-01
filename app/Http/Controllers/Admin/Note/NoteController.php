<?php

namespace App\Http\Controllers\Admin\Note;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleDesc;
use App\Models\Note;
use App\Service\PushService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request)
    {
        return view('admin.note.lists');
    }
    
    /**
     * 列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listsAjax(Request $request)
    {
        $limit   = $request->input('limit', 10);
        $keyword = $request->input('keyword');
        
        //搜索
        $where = [];
        if ($keyword) {
            $where[] = ['title', 'like', '%' . $keyword . '%'];
        }
        
        $result = Note::select('id', 'title', 'desc', 'position', 'created_at', 'status')
            ->where($where)
            ->orderBy('id', 'desc')
            ->paginate($limit)->toArray();
        if (!$result['data']) {
            return res_error('数据为空');
        }
        
        return res_success($result['data'], $result['total']);
    }
    
    /**
     * 添加编辑
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('post')) {
            //验证规则
            $validator = Validator::make($request->all(), [
                'title' => 'required',
            ], [
                'title.required' => '标题不能为空',
            ]);
            $error     = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }
            
            $save_data = [];
            foreach ($request->only(['title', 'desc']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }
            
            try {
                $res = DB::transaction(function () use ($request, $save_data) {
                    if ($request->id) {
                        $res = Note::where('id', $request->id)->update($save_data);
                    } else {
                        $res = Note::create($save_data);
                    }
                    
                    return $res;
                });
            } catch (\Exception $e) {
                $res = false;
            }
            
            if ($res) {
                return res_success();
            } else {
                return res_error('保存失败');
            }
        } else {
            $item = [];
            if ($request->id) {
                $item = Note::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }
            
            return view('admin.note.edit', ['item' => $item]);
        }
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
            $res = Note::whereIn('id', $ids)->update(['status' => $status]);
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
    public function delete(Request $request)
    {
        $id = $request->id;
        if (is_array($request->id)) {
            foreach ($id as $val) {
                $ids[] = (int)$val;
            }
        } else {
            $ids = [(int)$id];
        }
        
        if (!$ids) {
            return res_error('参数错误');
        }
        
        try {
            $res = DB::transaction(function () use ($ids) {
                $res = Note::whereIn('id', $ids)->delete();
                
                return $res;
            });
        } catch (\Exception $e) {
            $res = false;
        }
        
        if ($res) {
            return res_success();
        } else {
            return res_error('删除失败');
        }
    }
    
    /**
     * 修改排序
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function position(Request $request)
    {
        $id       = (int)$request->id;
        $position = (int)$request->position;
        if ($id && isset($position)) {
            $res = Note::where('id', $id)->update(['position' => $position]);
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