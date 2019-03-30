<?php
    /**
     * Created by PhpStorm.
     * User: wanghui
     * Date: 2018/5/14
     * Time: 下午1:28
     */

    namespace App\Http\Controllers\Admin\Member;

    use App\Http\Controllers\Controller;
    use App\Models\Areas;
    use App\Models\Member;
    use App\Models\MemberDepartment;
    use App\Models\MemberGroup;
    use App\Models\MemberProfile;
    use App\Models\MemberSimple;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Validation\Rule;
    use Validator;
    use Illuminate\Http\Request;

    /**
     * 会员管理
     * Class MemberController
     * @package App\Http\Controllers\Admin\Member
     */
    class MemberController extends Controller
    {
        /**
         * 列表
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function lists(Request $request)
        {
            $membergroup = MemberGroup::pluck('title', 'id');
            $department = MemberDepartment::getSelectCategory();

            return view('admin.member.member.lists', ['membergroup' => $membergroup, 'department' => $department]);
        }


        /**
         * 列表ajax数据
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function listsAjax(Request $request)
        {
            $limit = $request->input('limit', 10);
            $tel = $request->input('tel');
            $member_id = $request->input('member_id');
            $group_id = $request->input('group_id');
            $member_type = $request->input('member_type');
            $depart_id = $request->input('depart_id');
            $is_live = $request->input('is_live');
            $full_name = $request->input('full_name');//真实姓名
            //搜索
            $where = [];
            if ($tel) {
                $user_id = Member::where('username', $tel)->value('id');
                $where[] = ['member_id', $user_id];
            }
            if ($member_id) {
                $where[] = ['member_id', $member_id];
            }
            if ($group_id) {
                $where[] = ['group_id', $group_id];
            }
            if ($member_type) {
                $where[] = ['type', $member_type];
            }
            if ($depart_id) {
                $where[] = ['depart_id', $depart_id];
            }
            if ($full_name) {
                $where[] = ['full_name', $full_name];
            }
            if (is_numeric($is_live) === true) {
                $where[] = ['is_live', $is_live];
            }

            //$result = MemberProfile::select('id', 'username', 'nick_name', 'headimg', 'created_at', 'status')

            $result = MemberProfile::select('talent_show', 'is_live', 'show_live_msg', 'member_id')
                ->where($where)
                ->orderBy('member_id', 'desc')
                ->paginate($limit)->toArray();
            if (!$result['data']) {
                return res_error('数据为空');
            }

            $user_ids = [];
            foreach ($result['data'] as $value) {
                $user_ids[] = $value['member_id'];
            }

            if ($user_ids) {
                $user = Member::whereIn('id', $user_ids)
                    ->select('username', 'nick_name', 'headimg', 'created_at', 'status', 'id')->get()->toArray();

                $user = array_column($user, null, 'id');
            }
            $data_list = [];
            foreach ($result['data'] as $key => $value) {
                $_item = $value;
                $_item['username'] = isset($user[$value['member_id']]['username']) ? $user[$value['member_id']]['username'] : '';
                $_item['nick_name'] = isset($user[$value['member_id']]['nick_name']) ? $user[$value['member_id']]['nick_name'] : '';
                $_item['headimg'] = isset($user[$value['member_id']]['headimg']) ? $user[$value['member_id']]['headimg'] : '';
                $_item['created_at'] = isset($user[$value['member_id']]['created_at']) ? $user[$value['member_id']]['created_at'] : '';
                $_item['status'] = isset($user[$value['member_id']]['status']) ? $user[$value['member_id']]['status'] : '';
                $data_list[] = $_item;
            }

            return res_success($data_list, $result['total']);
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
                    'username' => [
                        'required',
                        Rule::unique('member')->ignore($request->id),
                    ],
                    'group_id' => 'required|numeric',
                ], [
                                                 'username.required' => '用户名不能为空',
                                                 'username.unique'   => '用户已经存在',
                                                 'group_id.required' => '用户组不能为空',
                                                 'group_id.numeric'  => '用户组只能是数字',
                                             ]);
                $error = $validator->errors()->all();
                if ($error) {
                    return res_error(current($error));
                }

                $save_data = [];
                foreach ($request->only(['username', 'nick_name', 'headimg']) as $key => $value) {
                    $save_data[$key] = ($value || $value == 0) ? $value : null;
                }

                $profile_data = [];
                foreach ($request->only(['group_id', 'full_name', 'tel', 'email', 'sex', 'prov_id', 'city_id', 'area_id', 'type', 'depart_id']) as $key => $value) {
                    $profile_data[$key] = ($value || $value == '0') ? $value : '';
                    if (!$profile_data[$key]) {
                        unset($profile_data[$key]);
                    }
                }
                //省市区处理
                if (isset($profile_data['prov_id'])) {
                    $profile_data['prov_name'] = Areas::getAreaName($profile_data['prov_id']);
                }
                if (isset($profile_data['city_id'])) {
                    $profile_data['city_name'] = Areas::getAreaName($profile_data['city_id']);
                }
                if (isset($profile_data['area_id'])) {
                    $profile_data['area_name'] = Areas::getAreaName($profile_data['area_id']);
                }

                $password = $request->password;
                if ($password) {
                    $save_data['password'] = Hash::make($password);
                }
                if (!$request->id && !$password) {
                    return res_error('密码不能为空');
                }

                try {
                    $res = DB::transaction(function () use ($request, $save_data, $profile_data) {
                        if ($request->id) {
                            $res = Member::where('id', $request->id)->update($save_data);
                            MemberProfile::where('member_id', $request->id)->update($profile_data);
                        } else {
                            $result = Member::create($save_data);
                            $res = $result->id;
                            $profile_data['member_id'] = $res;
                            MemberProfile::create($profile_data);
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
                    $item = Member::find($request->id)->toArray();
                    if (!$item) {
                        return res_error('数据错误');
                    }
                    //资料
                    $profile = Member::find($request->id)->profile;
                    if ($profile) {
                        $profile = $profile->toArray();
                        $item = array_merge($item, $profile);
                    }
                }
                //查询用户组
                $group = MemberGroup::all();

                return view('admin.member.member.edit', ['item' => $item, 'group' => $group]);
            }
        }


        /**
         * 添加编辑-会员简介人物图
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
         */
        public function simpleEdit(Request $request)
        {
            if ($request->isMethod('post')) {
                //验证规则
                $validator = Validator::make($request->all(), [
                    'image' => 'required',
                    'desc'  => 'required',
                ], [
                                                 'image.required' => '头像不能为空',
                                                 'desc.required'  => '简介不能为空',
                                             ]);
                $error = $validator->errors()->all();
                if ($error) {
                    return res_error(current($error));
                }

                $save_data = [];
                foreach ($request->only(['image', 'desc', 'member_id']) as $key => $value) {
                    $save_data[$key] = ($value || $value == 0) ? $value : null;
                }

                try {
                    $res = DB::transaction(function () use ($request, $save_data) {
                        $in_data = MemberSimple::where('member_id', $request->member_id)->first();
                        if ($in_data) {
                            $res = MemberSimple::where('member_id', $request->member_id)->update($save_data);
                        } else {
                            $res = MemberSimple::create($save_data);
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
                if ($request->member_id) {
                    $item = MemberSimple::where('member_id', $request->member_id)->first();
                    if ($item) {
                        $item = $item->toArray();
                    }
                }
                $item['member_id'] = $request->member_id;

                return view('admin.member.member.simple-edit', ['item' => $item]);
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
                $res = Member::whereIn('id', $ids)->update(['status' => $status]);
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
         * 修改达人状态
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function talent(Request $request)
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
                $res = MemberProfile::whereIn('member_id', $ids)->update(['talent_show' => $status]);
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
         * 修改直播状态
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function live(Request $request)
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
                $res = MemberProfile::whereIn('member_id', $ids)->update(['is_live' => $status]);
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
         * 修改直播观看状态
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function live_msg(Request $request)
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
                $res = MemberProfile::whereIn('member_id', $ids)->update(['show_live_msg' => $status]);
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

            $res = Member::whereIn('id', $ids)->delete();
            if ($res) {
                return res_success();
            } else {
                return res_error('删除失败');
            }
        }

        /**
         * 获取部门信息
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function department(Request $request)
        {
            $parent_id = (int)$request->parent_id;
            $area = MemberDepartment::where('parent_id', $parent_id)
                ->select('id', 'title')
                ->orderBy('id', 'asc')
                ->get()->toArray();

            return res_success($area);
        }
    }