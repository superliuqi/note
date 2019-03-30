<?php

    namespace App\Http\Controllers\Admin\System;

    use App\Models\Gift;
    use Validator;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    class GiftController extends Controller
    {
        /**
         * 列表
         */
        public function lists(Request $request)
        {
            return view('admin.system.gift.lists');
        }

        /**
         * 列表ajax数据
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function listsAjax(Request $request)
        {
            $limit = $request->input('limit', 10);
            $keyword = $request->input('keyword');
            $type = $request->input('type');

            //搜索
            $where = [];
            if ($keyword) {
                $where[] = ['name', 'like', '%' . $keyword . '%'];
            }
            if ($type) {
                $where[] = ['type', '=', $type];
            }
            $result = Gift::select('id', 'name', 'image', 'amount', 'position', 'unit', 'created_at', 'status', 'type', 'is_gif_additional')
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
                    'name'            => 'required',
                    'image'           => 'required',
                    'small_gif_image' => 'required',
                    'gif_image'       => 'required',
                    'unit'            => 'required',
                    'amount'          => 'numeric',
                    'position'        => 'numeric',
                ], [
                                                 'name.required'            => '礼物名称不能为空',
                                                 'image.required'           => '图片不能为空',
                                                 'small_gif_image.required' => '小git图不能为空',
                                                 'gif_image.required'       => 'git图不能为空',
                                                 'unit.numeric'             => '单位不能为空',
                                                 'amount.numeric'           => '美币只能是数字',
                                                 'position.numeric'         => '排序只能是数字',
                                             ]);
                $error = $validator->errors()->all();
                if ($error) {
                    return res_error(current($error));
                }

                $save_data = [];
                foreach ($request->only(['name', 'image', 'small_gif_image', 'gif_image', 'amount', 'is_gif_additional', 'type', 'unit', 'position', 'status']) as $key => $value) {
                    $save_data[$key] = ($value || $value == 0) ? $value : null;
                }

                if ($request->id) {
                    $res = Gift::where('id', $request->id)->update($save_data);
                } else {
                    $result = Gift::create($save_data);
                    $res = $result->id;
                }
                if ($res) {
                    return res_success();
                } else {
                    return res_error('保存失败');
                }
            } else {
                $item = [];
                if ($request->id) {
                    $item = Gift::find($request->id);
                    if (!$item) {
                        return res_error('数据错误');
                    }
                }

                return view('admin.system.gift.edit', ['item' => $item]);
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
                $res = Gift::whereIn('id', $ids)->update(['status' => $status]);
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
        public function position(Request $request)
        {
            $id = (int)$request->id;
            $position = (int)$request->position;
            if ($id && isset($position)) {
                $res = Gift::where('id', $id)->update(['position' => $position]);
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
