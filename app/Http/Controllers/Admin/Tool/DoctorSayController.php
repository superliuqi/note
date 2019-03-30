<?php

    namespace App\Http\Controllers\Admin\Tool;

    use PhpParser\Comment\Doc;
    use Validator;
    use App\Models\DoctorSay;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    class DoctorSayController extends Controller
    {
        public function lists()
        {
            return view('admin.tool.doctor_say.lists');
        }

        /**
         * 列表ajax数据
         */
        public function listsAjax(Request $request)
        {
            $limit = $request->input('limit', 10);
            $keyword = $request->input('keyword');

            $where = [];
            if ($keyword) {
                $where[] = ['title', 'like', '%' . $keyword . '%'];
            }


            $result = DoctorSay::select('id', 'title', 'sub_title', 'created_at', 'is_rem', 'status', 'lang', 'name')
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
         */
        public function edit(Request $request)
        {
            if ($request->isMethod('post')) {
                //验证规则
                $validator = Validator::make($request->all(), [
                    'title'     => 'required',
                    'sub_title' => 'required',
                    'name'      => 'required',
                    'image'     => 'url',
                    'level'     => 'numeric',
                    'lang'      => 'numeric',
                    'position'  => 'numeric',
                    'url'       => 'url',
                ], [
                     'title.required'     => '标题不能为空',
                     'sub_title.required' => '副标题不能为空',
                     'name.required'      => '医生名称不能为空',
                     'image.url'          => '医生头像不正确',
                     'level.numeric'      => '医生等级不正确',
                     'lang.numeric'       => '请选择正确的语言',
                     'position.numeric'   => '排序只能是数字',
                     'url.url'            => '请填写正确的视频地址',
                ]);

                $error = $validator->errors()->all();
                if ($error) {
                    return res_error(current($error));
                }

                $save_data = [];
                foreach ($request->only(['title', 'sub_title', 'name', 'image', 'level', 'lang', 'position', 'url', 'is_rem', 'subject', 'content']) as $key => $val) {
                    $save_data[$key] = ($val || $val == 0) ? $val : null;
                }

                if ($request->id) {
                    $res = DoctorSay::where('id', $request->id)->update($save_data);
                } else {
                    $res = DoctorSay::create($save_data);
                }

                if ($res) {
                    return res_success();
                } else {
                    return res_error('保存失败');
                }
            } else {
                $item = [];
                $lang = config('app.config_lang');
                if ($request->id) {
                    $item = DoctorSay::find($request->id);
                    if (!$item) {
                        return res_error('数据错误');
                    }
                }

                return view('admin.tool.doctor_say.edit', ['item' => $item, 'lang' => $lang]);
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
                $res = DoctorSay::whereIn('id', $ids)->update(['status' => $status]);
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

            $res = DoctorSay::whereIn('id', $ids)->delete();
            if ($res) {
                return res_success();
            } else {
                return res_error('删除失败');
            }
        }
    }
