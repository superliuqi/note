<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午4:28
 */

namespace App\Http\Controllers\Admin\President;

use App\Http\Controllers\Controller;
use App\Models\YuAlbum;
use App\Models\YuAlbumPhoto;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 相册管理
 * Class YuVideoController
 * @package App\Http\Controllers\Admin\President
 */
class YuAlbumController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        return view('admin.president.yualbum.lists');
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
        $result = YuAlbum::select('id', 'title', 'image', 'position', 'created_at', 'status')
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
                'image' => 'required',
            ], [
                'title.required' => '标题不能为空',
                'image.required' => '图片地址不能为空',
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['title', 'image','lang','position']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }

            try {
                $res = DB::transaction(function () use ($request, $save_data) {
                    if ($request->id) {
                        $res = YuAlbum::where('id', $request->id)->update($save_data);
                        YuAlbumPhoto::where('album_id',$request->id)->delete();
                        foreach ($request->images as $key=>$v){
                            $image_list = array();
                            $image_list = array(
                                'album_id'      => $request->id,
                                'image'       => $v,
                                'width'     => getimagesize($v)[0],
                                'height'    => getimagesize($v)[1],
                            );
                            YuAlbumPhoto::create($image_list);
                        }
                    } else {
                        $result = YuAlbum::create($save_data);
                        $res = $result->id;
                        if($request->images){
                            foreach ($request->images as $key=>$v){
                                $image_list = array();
                                $image_list = array(
                                    'album_id'      => $res,
                                    'image'       => $v,
                                    'width'     => getimagesize($v)[0],
                                    'height'    => getimagesize($v)[1]
                                );
                                YuAlbumPhoto::create($image_list);
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
                $item = YuAlbum::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }

                $item['goods_image'] = $item->image()->pluck('image')->toArray();//查询图片集
            }
            return view('admin.president.yualbum.edit', ['item' => $item]);
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
            $res = YuAlbum::whereIn('id', $ids)->update(['status' => $status]);
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
                $res = YuAlbum::whereIn('id', $ids)->delete();
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
            $res = YuAlbum::where('id', $id)->update(['position' => $position]);
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