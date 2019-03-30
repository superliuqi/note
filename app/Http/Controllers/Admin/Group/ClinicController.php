<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午4:28
 */

namespace App\Http\Controllers\Admin\Group;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\ClinicCity;
use App\Models\ClinicPhoto;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 相册管理
 * Class YuVideoController
 * @package App\Http\Controllers\Admin\President
 */
class ClinicController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        return view('admin.group.clinic.lists');
    }

    /**
     * 列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listsAjax(Request $request) {
        $limit = $request->input('limit', 10);
        $keyword = $request->input('keyword');
        $category_id = '';
        //搜索
        $where = array();
        if ($keyword) {
            $where[] = array('title', 'like', '%' . $keyword . '%');
        }
        $result = Clinic::select('id', 'title', 'image','desc','level', 'position', 'created_at', 'status')
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
            //dd($request->all());
            //验证规则
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'address' => 'required',
                'longitude' => 'required',
                'latitude' => 'required',
                'level' => 'required',
                //'image' => 'required',
                'city_id'=>'required',
                'tags'=>'required',
            ], [
                'title.required' => '标题不能为空',
                'address.required' => '地址不能为空',
                'longitude.required' => '经度不能为空',
                'latitude.required' => '纬度不能为空',
                'level.required' => '等级不能为空',
                //'image.required' => '图片不能为空',
                'city_id.required' => '城市不能为空',
                'tags.required' => '标签不能为空',
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['title','address','longitude','latitude','level', 'image','city_id',
                'lang','desc','content','position']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }
            $tag=implode(',',array_keys($request->tags));
            $save_data['tags']=$tag;

            try {
                $res = DB::transaction(function () use ($request, $save_data) {
                    if ($request->id) {
                        $res = Clinic::where('id', $request->id)->update($save_data);
                        ClinicPhoto::where('clinic_id',$request->id)->delete();
                        foreach ($request->images as $key=>$v){
                            $image_list = array();
                            $image_list = array(
                                'clinic_id'      => $request->id,
                                'image'       => $v,
                                // 'width'     => $request->images['width'][$key],
                                // 'height'    => $request->images['height'][$key],
                            );
                            ClinicPhoto::create($image_list);
                        }
                    } else {
                        $result = Clinic::create($save_data);
                        $res = $result->id;
                        if($request->images){
                            foreach ($request->images as $key=>$v){
                                $image_list = array();
                                $image_list = array(
                                    'clinic_id'      => $res,
                                    'image'       => $v,
                                    // 'width'     => $request->images['width'][$key],
                                    // 'height'    => $request->images['height'][$key],
                                );
                                ClinicPhoto::create($image_list);
                            }
                        }
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
                $item = Clinic::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }

                $item['goods_image'] = $item->image()->pluck('image')->toArray();//查询图片集
            }
            if($item['tags']){
                $item['tags']=explode(',',$item['tags']);
            }
            
            $city=ClinicCity::all();
            $tag=Tag::where('type','1')->get();
            return view('admin.group.clinic.edit', ['item' => $item,'city'=>$city,'tag'=>$tag]);
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
            $res = Clinic::whereIn('id', $ids)->update(['status' => $status]);
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
                $res = Clinic::whereIn('id', $ids)->delete();
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
            $res = Clinic::where('id', $id)->update(['position' => $position]);
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