<?php

    namespace App\Http\Controllers\Admin\Tool;

    use App\Models\Tag;
    use Validator;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    class TagController extends Controller
    {
        public function lists()
        {
            return view('admin.tool.tag.lists');
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

            //搜索
            $where = [];
            if ($keyword) {
                $where[] = ['name', 'like', '%' . $keyword . '%'];
            }
            $result = Tag::select('id', 'name', 'position', 'created_at', 'status', 'type')
                ->where($where)
                ->orderBy('id', 'desc')
                ->paginate($limit)->toArray();
            if (!$result['data']) {
                return res_error('数据为空');
            }
            foreach ($result['data'] as $k => $v) {
                $result['data'][$k]['type'] = $v['type'] == 1 ? '医院机构，医生，日记' : ($v['type'] == 2 ? '圈子' : '');
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
                    'name'     => 'required',
                    'type'     => 'numeric',
                    'status'   => 'numeric',
                    'position' => 'numeric',
                ], [
                                                 'name.required'    => '礼物名称不能为空',
                                                 'type.numeric'     => '类型有误',
                                                 'status.numeric'   => '状态有误',
                                                 'position.numeric' => '排序只能是数字',
                                             ]);
                $error = $validator->errors()->all();
                if ($error) {
                    return res_error(current($error));
                }

                $save_data = [];
                foreach ($request->only(['name', 'type', 'status', 'position']) as $key => $value) {
                    $save_data[$key] = ($value || $value == 0) ? $value : null;
                }

                if ($request->id) {
                    $res = Tag::where('id', $request->id)->update($save_data);
                } else {
                    $result = Tag::create($save_data);
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
                    $item = Tag::find($request->id);
                    if (!$item) {
                        return res_error('数据错误');
                    }
                }

                return view('admin.tool.tag.edit', ['item' => $item]);
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
                $res = Tag::whereIn('id', $ids)->update(['status' => $status]);
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
                $res = Tag::where('id', $id)->update(['position' => $position]);
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
