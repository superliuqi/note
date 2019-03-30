<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午4:28
 */

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 商品规格管理
 * Class ArticleController
 * @package App\Http\Controllers\Admin\Tool
 */
class AttributeController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        //查询分类
        $category = Category::getSelectCategory();
        return view('admin.goods.attribute.lists', ['category' => $category]);
    }

    /**
     * 列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listsAjax(Request $request) {
        $limit = $request->input('limit', 10);
        $keyword = $request->input('keyword');
        $category_id = $request->input('category_id');

        //搜索
        $where = array();
        if ($keyword) {
            $where[] = array('title', 'like', '%' . $keyword . '%');
        }
        if ($category_id) {
            $where[] = array('category_id', $category_id);
        }
        $result = Attribute::select('id', 'title', 'category_id', 'note', 'position', 'created_at')
            ->where($where)
            ->orderBy('id', 'desc')
            ->paginate($limit)->toArray();
        if (!$result['data']) {
            return res_error('数据为空');
        }
        $category_ids = array();
        foreach ($result['data'] as $value) {
            $category_ids[] = $value['category_id'];
        }
        if ($category_ids) {
            $category = Category::whereIn('id', $category_ids)->select('id', 'title')->get();
            if (!$category->isEmpty()) {
                $category = $category->toArray();
                $category = array_column($category, 'title', 'id');
            }
        }
        $data_list = array();
        foreach ($result['data'] as $key => $value) {
            $_item = $value;
            $_item['category_name'] = isset($category[$value['category_id']]) ? $category[$value['category_id']] : '';
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
                'title' => 'required',
                'category_id' => 'required|numeric',
                'input_type' => 'required',
                'position' => 'required|numeric',
            ], [
                'title.required' => '规格名称不能为空',
                'category_id.required' => '分类不能为空',
                'category_id.numeric' => '分类只能是数字',
                'input_type.required' => '类型不能为空',
                'position.required' => '排序不能为空',
                'position.numeric' => '排序只能是数字',
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['title', 'input_type', 'category_id', 'note', 'position']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }

            if ($request->id) {
                $res = Attribute::where('id', $request->id)->update($save_data);
            } else {
                $result = Attribute::create($save_data);
                $res = $result->id;
            }
            if ($res) {
                return res_success();
            } else {
                return res_error('保存失败');
            }
        } else {
            $item = array();
            if ($request->id) {
                $item = Attribute::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }
            //查询分类
            $category = Category::getSelectCategory();
            return view('admin.goods.attribute.edit', ['item' => $item, 'category' => $category]);
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
            $res = Attribute::whereIn('id', $ids)->update(['status' => $status]);
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

        $res = Attribute::whereIn('id', $ids)->delete();
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
            $res = Attribute::where('id', $id)->update(['position' => $position]);
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