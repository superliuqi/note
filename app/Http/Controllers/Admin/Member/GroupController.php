<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/14
 * Time: 下午1:10
 */

namespace App\Http\Controllers\Admin\Member;

use App\Http\Controllers\Controller;
use App\Models\MemberGroup;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 会员用户组
 * Class GroupController
 * @package App\Http\Controllers\Admin\Member
 */
class GroupController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        return view('admin.member.group.lists');
    }

    /**
     * 列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listsAjax(Request $request) {
        $limit = $request->input('limit', 10);
        $keyword = $request->input('keyword');

        //搜索
        $where = array();
        if ($keyword) {
            $where[] = array('title', 'like', '%' . $keyword . '%');
        }
        $result = MemberGroup::select('id', 'title', 'created_at')
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
    public function edit(Request $request) {
        if ($request->isMethod('post')) {
            //验证规则
            $validator = Validator::make($request->all(), [
                'title' => 'required'
            ], [
                'title.required' => '菜单名称不能为空'
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['title']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }

            if ($request->id) {
                $res = MemberGroup::where('id', $request->id)->update($save_data);
            } else {
                $result = MemberGroup::create($save_data);
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
                $item = MemberGroup::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }
            return view('admin.member.group.edit', ['item' => $item]);
        }
    }

    /**
     * 删除数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request) {
        $id = (int)$request->id;

        if (!$id) {
            return res_error('参数错误');
        }

        $res = MemberGroup::where('id', $id)->delete();
        if ($res) {
            return res_success();
        } else {
            return res_error('删除失败');
        }
    }
}