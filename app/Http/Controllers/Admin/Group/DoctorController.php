<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午4:28
 */

namespace App\Http\Controllers\Admin\Group;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 视频管理
 * Class DoctorController
 * @package App\Http\Controllers\Admin\Group
 */
class DoctorController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        $type=$request->type;
        return view('admin.group.doctor.lists',['type'=>$type]);
    }

    /**
     * 列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listsAjax(Request $request) {
        $limit = $request->input('limit', 10);
        $keyword = $request->input('keyword');
        $type = (int)$request->type;
        //搜索
        $where = array();
        $where[] = array('type', $type);
        if ($keyword) {
            $where[] = array('title', 'like', '%' . $keyword . '%');
        }

        $result = Doctor::select('id','name','head_img',
            'bg_image', 'detail_image', 'operation_num','created_at', 'position', 'fans_num', 'status')
            ->where($where)
            ->orderBy('id', 'desc')
            ->paginate($limit)->toArray();
        if (!$result['data']) {
            return res_error('数据为空');
        }

        $data_list = array();
        foreach ($result['data'] as $key => $value) {
            $_item = $value;
            $data_list[] = $_item;
        }
        return res_success($data_list, $result['total']);
    }

    /**
     * 添加编辑
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function edit(Request $request) {
        if ($request->isMethod('post')) {
            //验证规则
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'en_name' => 'required',
                'head_img'=>'required',
                'bg_image'=>'required',
                'bg_image_full'=>'required',
                'detail_image'=>'required',
                'operation_num'=>'required',
                'fans_num'=>'required',

            ], [
                'name.required' => '姓名不能为空',
                'en_name.required' => '英文名不能为空',
                'head_img.required' => '头像不能为空',
                'bg_image.required' => '背景图不能为空',
                'bg_image_full.required' => '全面屏背景图不能为空',
                'detail_image.required' => '详情图不能为空',
                'operation_num.required' => '操作数不能为空',
                'fans_num.required' => '粉丝数不能为空',
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['name','en_name', 'head_img', 'bg_image','bg_image_full',
                'detail_image','operation_num','fans_num','label','position','lang']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }

            try {
                $res = DB::transaction(function () use ($request, $save_data) {
                    if ($request->id) {
                        $res = Doctor::where('id', $request->id)->update($save_data);
                    } else {
                        $result = Doctor::create($save_data);
                        $res = $result->id;
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
            $item = array();
            if ($request->id) {
                $item = Doctor::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }
            return view('admin.group.doctor.edit', ['item' => $item]);
        }
    }

    /**
     * 修改状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request) {
        $id = $request->id;
        if (is_array($request->id)) {
            foreach ($id as $val) {
                $ids[] = (int)$val;
            }
        } else {
            $ids = array((int)$id);
        }
        $status = (int)$request->status;
        if ($ids && isset($status)) {
            $res = Doctor::whereIn('id', $ids)->update(['status' => $status]);
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

        try {
            $res = DB::transaction(function () use ($ids) {
                $res = Doctor::whereIn('id', $ids)->delete();
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
    public function position(Request $request) {
        $id = (int)$request->id;
        $position = (int)$request->position;
        if ($id && isset($position)) {
            $res = Doctor::where('id', $id)->update(['position' => $position]);
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