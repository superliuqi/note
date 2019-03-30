<?php
/**
 * Created by PhpStorm.
 * User: liubin
 * Date: 2018/12/04
 * Time: 下午1:28
 */

namespace App\Http\Controllers\Admin\h5;

use App\Http\Controllers\Controller;
use App\Models\H5\H5SignAuth;
use App\Models\H5\H5SignChief;
use App\Models\H5\H5SignRecord;
use App\Models\H5\H5SignService;
use App\Models\H5\H5SignShoper;
use App\Models\MemberProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * H5-签到系统
 * Class MemberController
 * @package App\Http\Controllers\Admin\Member
 */
class SignController extends Controller
{
    /**
     * 服务商列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function service_lists(Request $request)
    {
        return view('admin.h5.sign.service_lists');
    }


    /**
     * 服务商列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function service_listsAjax(Request $request)
    {
        $limit   = $request->input('limit', 10);
        $keyword = $request->input('keyword');

        //搜索
        $where = [];
        if ($keyword) {
            $where[] = ['name', 'like', '%' . $keyword . '%'];
        }

        $result = H5SignService::select('*')
            ->where($where)
            ->orderBy('id', 'desc')
            ->paginate($limit)->toArray();
        if (!$result['data']) {
            return res_error('数据为空');
        }


        $data_list = [];

        foreach ($result['data'] as $key => $value) {
            $_item      = $value;
            $group_name = '';
            if ($value['group']) {
                $group_name = H5SignRecord::GROUP[ $value['group'] ];
            }
            $_item['group_name'] = $group_name;
            $data_list[]         = $_item;
        }


        return res_success($data_list, $result['total']);
    }

    /**
     * 修改店家数量
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function service_shoper_num(Request $request)
    {
        $id         = (int)$request->id;
        $shoper_num = (int)$request->shoper_num;
        if ($id && isset($shoper_num)) {
            $res = H5SignService::where('id', $id)->update(['shoper_num' => $shoper_num]);
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
     * 修改服务商状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function service_status(Request $request)
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
            $res = H5SignService::whereIn('id', $ids)->update(['status' => $status]);
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
     * 修改服务商分组
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function service_group(Request $request)
    {
        $id = $request->id;
        if (is_array($request->id)) {
            foreach ($id as $val) {
                $ids[] = (int)$val;
            }
        } else {
            $ids = [(int)$id];
        }

        $group = (int)$request->group;
        if ($ids && isset($group)) {
            $res = H5SignService::whereIn('id', $ids)->update(['group' => $group]);
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
     * 服务商删除数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function service_delete(Request $request)
    {
        $id  = $request->id;
        $ids = '';
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

        $res = H5SignService::whereIn('id', $ids)->delete();
        if ($res) {
            return res_success();
        } else {
            return res_error('删除失败');
        }
    }


    /**
     * 修改店家陪同分组
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shoper_group(Request $request)
    {
        $id = $request->id;
        if (is_array($request->id)) {
            foreach ($id as $val) {
                $ids[] = (int)$val;
            }
        } else {
            $ids = [(int)$id];
        }

        $group = (int)$request->group;
        if ($ids && isset($group)) {
            $res = H5SignShoper::whereIn('id', $ids)->update(['group' => $group]);
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
     * 店家列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function shop_owner_lists(Request $request)
    {
        $service = H5SignService::where('status', 1)->pluck('name', 'id')->toArray();

        return view('admin.h5.sign.shop_owner_lists', ['service' => $service]);
    }


    /**
     * 店家列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shop_owner_listsAjax(Request $request)
    {
        $limit      = $request->input('limit', 10);
        $service_id = (int)$request->input('service_id');
        $keyword    = $request->input('keyword');

        //搜索
        $where = ['type' => '1'];

        if ($service_id) $where[] = ['service_id', $service_id];

        if ($keyword) {
            $where[] = ['name', 'like', '%' . $keyword . '%'];
        }

        $result = H5SignShoper::select('*')
            ->where($where)
            ->orderBy('id', 'desc')
            ->paginate($limit)->toArray();
        if (!$result['data']) {
            return res_error('数据为空');
        }

        $service_ids = []; //服务商ID
        foreach ($result['data'] as $value) {
            $service_ids[] = $value['service_id'];
        }

        if ($service_ids) {
            $shoper_service_res = H5SignService::whereIn('id', $service_ids)->pluck('name', 'id');//服务商
            if (!$shoper_service_res->isEmpty()) {
                $shoper_service = $shoper_service_res->toArray();
            }
        }
        $chief_ids = []; //课长ID
        foreach ($result['data'] as $value) {
            $chief_ids[] = $value['section_chief'];
        }

        if ($chief_ids) {
            $shoper_chief_res = H5SignChief::whereIn('id', $chief_ids)->pluck('name', 'id');//课长
            if (!$shoper_chief_res->isEmpty()) {
                $shoper_chief = $shoper_chief_res->toArray();
            }
        }

        $data_list = [];

        foreach ($result['data'] as $key => $value) {
            $_item      = $value;
            $group_name = '';
            if ($value['group']) {
                $group_name = H5SignRecord::GROUP[ $value['group'] ];
            }

            $_item['service_name'] = isset($shoper_service[ $value['service_id'] ]) ? $shoper_service[ $value['service_id'] ] : '';
            $_item['chief_name']   = isset($shoper_chief[ $value['section_chief'] ]) ? $shoper_chief[ $value['section_chief'] ] : '';
            $_item['group_name']   = $group_name;
            $data_list[]           = $_item;
        }

        return res_success($data_list, $result['total']);
    }


    /**
     * 店家 添加/编辑
     * @param Request $request
     * create by: liuqi
     * Date:
     */
    public function shop_owner_edit(Request $request)
    {
        if ($request->isMethod('post')) {
            //验证规则
            $validator = Validator::make($request->all(), [
                'name'          => [
                    'required',
                    Rule::unique('h5_sign_shoper')->ignore($request->id)
                ],
                'section_chief' => 'required',
                'service_id'    => 'required',
                'area'          => 'required',
            ], [
                'name.required'          => '姓名不能为空',
                'name.unique'            => '姓名已经存在',
                'section_chief.required' => '课长不能为空',
                'service_id.required'    => '服务商不能为空',
                'area.required'          => '大区不能为空',
            ]);

            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $shoper_num = H5SignService::where('id', $request->service_id)->value('shoper_num');//可以添加的店家总数

            $exist_shoper = H5SignShoper::where(['type' => 1, 'service_id' => $request->service_id])->count(); //已经存在的店家

            if ($request->id) {
                $exist_shoper_date = H5SignShoper::where(['type' => 1, 'service_id' => $request->service_id])->pluck('id');
                if ($exist_shoper_date) {
                    $exist_shoper_date = $exist_shoper_date->toArray();
                    $isin              = in_array($request->id, $exist_shoper_date);
                    if (!$isin) {
                        return res_error('店家已上限');
                    }
                }
            } else {
                if ($shoper_num <= $exist_shoper) {
                    return res_error('店家已上限');
                }
            }


            $save_data = [];
            foreach ($request->only(['name', 'section_chief', 'service_id', 'area']) as $key => $val) {
                $save_data[ $key ] = ($val || $val == 0) ? $val : '';
            }

            try {
                $res = DB::transaction(function () use ($request, $save_data) {
                    if ($request->id) {
                        $res = H5SignShoper::where('id', $request->id)->update($save_data);
                    } else {
                        $save_data['type'] = '1';
                        $res               = H5SignShoper::create($save_data);
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
                $item = H5SignShoper::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }
            $service = H5SignService::where('status', 1)->get();
            if ($service) {
                $service = $service->toArray();
            }

            $chief = H5SignChief::where('status', 1)->get();
            if ($chief) {
                $chief = $chief->toArray();
            }

            return view('admin.h5.sign.shop_owner_edit', ['item' => $item, 'service' => $service, 'chief' => $chief]);
        }
    }


    /**
     * 修改店家状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shop_owner_status(Request $request)
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
            $res = H5SignShoper::whereIn('id', $ids)->update(['status' => $status]);
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
     * 店家删除数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function Shop_owner_delete(Request $request)
    {
        $id  = $request->id;
        $ids = '';
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

        $res = H5SignShoper::whereIn('id', $ids)->delete();
        if ($res) {
            return res_success();
        } else {
            return res_error('删除失败');
        }
    }


    /**
     * 陪同列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function accompany_lists(Request $request)
    {
        $service = H5SignService::where('status', 1)->pluck('name', 'id')->toArray();

        return view('admin.h5.sign.accompany_lists', ['service' => $service]);
    }


    /**
     * 陪同列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accompany_listsAjax(Request $request)
    {
        $limit      = $request->input('limit', 10);
        $service_id = (int)$request->input('service_id');
        $keyword    = $request->input('keyword');

        //搜索
        $where = ['type' => '2'];

        if ($service_id) $where[] = ['service_id', $service_id];

        if ($keyword) {
            $where[] = ['name', 'like', '%' . $keyword . '%'];
        }


        $result = H5SignShoper::select('*')
            ->where($where)
            ->orderBy('id', 'desc')
            ->paginate($limit)->toArray();
        if (!$result['data']) {
            return res_error('数据为空');
        }

        $service_ids = []; //服务商ID
        foreach ($result['data'] as $value) {
            $service_ids[] = $value['service_id'];
        }

        if ($service_ids) {
            $shoper_service_res = H5SignService::whereIn('id', $service_ids)->pluck('name', 'id');
            if (!$shoper_service_res->isEmpty()) {
                $shoper_service = $shoper_service_res->toArray();
            }
        }
        $data_list = [];
        foreach ($result['data'] as $key => $value) {
            $_item      = $value;
            $group_name = '';
            if ($value['group']) {
                $group_name = H5SignRecord::GROUP[ $value['group'] ];
            }
            $_item['service_name']  = isset($shoper_service[ $value['service_id'] ]) ? $shoper_service[ $value['service_id'] ] : '';
            $_item['identity_name'] = H5SignShoper::IDENTITY_DESC[ $value['identity'] ] ? : '';
            $_item['group_name']    = $group_name;
            $data_list[]            = $_item;
        }

        return res_success($data_list, $result['total']);
    }


    /**
     * 陪同 添加/编辑
     * @param Request $request
     * create by: liu
     * Date:
     */
    public function accompany_edit(Request $request)
    {
        if ($request->isMethod('post')) {
            //验证规则
            $validator = Validator::make($request->all(), [
                'name'       => [
                    'required',
                    Rule::unique('h5_sign_shoper')->ignore($request->id)
                ],
                'identity'   => 'required',
                'service_id' => 'required',
            ], [
                'name.required'       => '姓名不能为空',
                'name.unique'         => '姓名已经存在',
                'identity.required'   => '类型不能为空',
                'service_id.required' => '服务商不能为空',
            ]);

            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = [];
            foreach ($request->only(['name', 'identity', 'service_id', 'area']) as $key => $val) {
                $save_data[ $key ] = ($val || $val == 0) ? $val : '';
            }

            try {
                $res = DB::transaction(function () use ($request, $save_data) {
                    if ($request->id) {
                        $res = H5SignShoper::where('id', $request->id)->update($save_data);
                    } else {
                        $save_data['type'] = '2';
                        $res               = H5SignShoper::create($save_data);
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
                $item = H5SignShoper::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }
            $service = H5SignService::where('status', 1)->get();

            if ($service) {
                $service = $service->toArray();
            }

            return view('admin.h5.sign.accompany_edit', ['item' => $item, 'service' => $service]);
        }
    }


    /**
     * 修改陪同状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accompany_status(Request $request)
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
            $res = H5SignShoper::whereIn('id', $ids)->update(['status' => $status]);
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
     * 陪同删除数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accompany_delete(Request $request)
    {
        $id  = $request->id;
        $ids = '';
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

        $res = H5SignShoper::whereIn('id', $ids)->delete();
        if ($res) {
            return res_success();
        } else {
            return res_error('删除失败');
        }
    }


    /**
     * 中奖列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lottery_lists(Request $request)
    {
        return view('admin.h5.sign.lottery_lists');
    }


    /**
     * 中奖列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lottery_listsAjax(Request $request)
    {
        $limit   = $request->input('limit', 10);
        $keyword = $request->input('keyword');

        //搜索
        $where    = ['is_lucky' => 1];
        $data_ids = [];
        $wheres   = [];
        if ($keyword) {
            $wheres[]      = ['name', 'like', '%' . $keyword . '%'];
            $service_ids   = H5SignService::where($wheres)->pluck('id');
            $service_datas = H5SignAuth::whereIn('m_id', $service_ids)->where('type', 3)->pluck('id');
            if ($service_datas) {
                $service_datas = $service_datas->toArray();
            }
            $shoper_ids   = H5SignShoper::where($wheres)->pluck('id');
            $shoper_datas = H5SignAuth::whereIn('m_id', $shoper_ids)->where([[DB::Raw('type in(1,2)'), '1']])->pluck('id');
            if ($shoper_datas) {
                $shoper_datas = $shoper_datas->toArray();
            }
            $data_ids = array_merge($service_datas, $shoper_datas);
        }
        $result = H5SignAuth::select('*')
            ->where($where);
        if ($keyword) {
            $result = $result->whereIn('id', $data_ids);
        }
        $result = $result
            ->orderBy('id', 'desc')
            ->paginate($limit)->toArray();
        if (!$result['data']) {
            return res_error('数据为空');
        }

        $user_ids_service = []; //用户ID
        $user_ids_shoper  = [];//用户ID
        foreach ($result['data'] as $value) {
            if ($value['type'] == 3) {
                $user_ids_service[] = $value['m_id'];
            } else {
                $user_ids_shoper[] = $value['m_id'];
            }

        }

        if ($user_ids_service) {

            $user_service = H5SignService::whereIn('id', $user_ids_service)->pluck('name', 'id');
        }
        if ($user_ids_shoper) {

            $user_shoper = H5SignShoper::whereIn('id', $user_ids_shoper)->pluck('name', 'id');
        }

        $data_list = [];

        foreach ($result['data'] as $key => $value) {
            $_item = $value;

//            if($value['type']=='3'){
//                $_item['user_name']=H5SignService::where('id',$value['m_id'])->value('name');
//            }else{
//                $_item['user_name']=H5SignShoper::where('id',$value['m_id'])->value('name');
//            }
            if ($value['type'] == 3) {
                $_item['user_name'] = isset($user_service[ $value['m_id'] ]) ? $user_service[ $value['m_id'] ] : '';
            } else {
                $_item['user_name'] = isset($user_shoper[ $value['m_id'] ]) ? $user_shoper[ $value['m_id'] ] : '';
            }

            $data_list[] = $_item;
        }

        return res_success($data_list, $result['total']);
    }


    /**
     * 中奖 添加/编辑
     * @param Request $request
     * create by: liuqi
     * Date:
     */
    public function lottery_edit(Request $request)
    {
        if ($request->isMethod('post')) {
            //验证规则
            $validator = Validator::make($request->all(), [
                'name'          => [
                    'required',
                    Rule::unique('h5_sign_shoper')->ignore($request->id)
                ],
                'section_chief' => 'required',
                'service_id'    => 'required',
                'area'          => 'required',
            ], [
                'name.required'          => '姓名不能为空',
                'name.unique'            => '姓名已经存在',
                'section_chief.required' => '课长不能为空',
                'service_id.required'    => '服务商不能为空',
                'area.required'          => '大区不能为空',
            ]);

            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $shoper_num = H5SignService::where('id', $request->service_id)->value('shoper_num');//可以添加的店家总数

            $exist_shoper = H5SignShoper::where(['type' => 1, 'service_id' => $request->service_id])->count(); //已经存在的店家

            if ($request->id) {
                $exist_shoper_date = H5SignShoper::where(['type' => 1, 'service_id' => $request->service_id])->pluck('id');
                if ($exist_shoper_date) {
                    $exist_shoper_date = $exist_shoper_date->toArray();
                    $isin              = in_array($request->id, $exist_shoper_date);
                    if (!$isin) {
                        return res_error('店家已上限');
                    }
                }
            } else {
                if ($shoper_num <= $exist_shoper) {
                    return res_error('店家已上限');
                }
            }


            $save_data = [];
            foreach ($request->only(['name', 'section_chief', 'service_id', 'area']) as $key => $val) {
                $save_data[ $key ] = ($val || $val == 0) ? $val : '';
            }

            try {
                $res = DB::transaction(function () use ($request, $save_data) {
                    if ($request->id) {
                        $res = H5SignShoper::where('id', $request->id)->update($save_data);
                    } else {
                        $save_data['type'] = '1';
                        $res               = H5SignShoper::create($save_data);
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
                $item = H5SignShoper::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }
            $service = H5SignService::where('status', 1)->get();
            if ($service) {
                $service = $service->toArray();
            }

            $chief = H5SignChief::where('status', 1)->get();
            if ($chief) {
                $chief = $chief->toArray();
            }

            return view('admin.h5.sign.shop_owner_edit', ['item' => $item, 'service' => $service, 'chief' => $chief]);
        }
    }


    /**
     * 修改中奖状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lottery_status(Request $request)
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
            $res = H5SignShoper::whereIn('id', $ids)->update(['status' => $status]);
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
     * 中奖删除数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lottery_delete(Request $request)
    {
        $id  = $request->id;
        $ids = '';
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

        $res = H5SignShoper::whereIn('id', $ids)->delete();
        if ($res) {
            return res_success();
        } else {
            return res_error('删除失败');
        }
    }

}