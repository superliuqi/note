<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午4:28
 */

namespace App\Http\Controllers\Admin\Group;

use App\Http\Controllers\Controller;
use App\Models\DoctorVideo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 视频管理
 * Class DoctorVideoController
 * @package App\Http\Controllers\Admin\Group
 */
class DoctorVideoController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        $doctor_id = $request->doctor_id;
        return view('admin.group.doctorvideo.lists',['doctor_id' => $doctor_id]);
    }

    /**
     * 列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listsAjax(Request $request) {
        $limit = $request->input('limit', 10);
        $doctor_id = (int)$request->doctor_id;
        //搜索
        $where = array();
        $where[] = array('doctor_id', $doctor_id);
        $result = DoctorVideo::select('id','image','video', 'created_at')
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
                'image'=>'required',
                'video'=>'required',
            ], [
                'image.required' => '操作前图不能为空',
                'video.required' => '视频链接不能为空',
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['image','video','doctor_id']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }

            try {
                $res = DB::transaction(function () use ($request, $save_data) {
                    if ($request->id) {
                        $res = DoctorVideo::where('id', $request->id)->update($save_data);
                    } else {
                        $result = DoctorVideo::create($save_data);
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
                $item = DoctorVideo::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }

            $item['doctor_id'] = isset($item['doctor_id']) ? $item['doctor_id'] : $request->doctor_id;

            return view('admin.group.doctorvideo.edit', ['item' => $item]);
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
                $res = DoctorVideo::whereIn('id', $ids)->delete();
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


}