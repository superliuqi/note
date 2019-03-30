<?php

    namespace App\Http\Controllers\Admin\System;

    use App\Models\Push;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Validator;
    use App\Service\PushService;

    class PushController extends Controller
    {
        public function lists()
        {
            return view('admin.system.push.lists');
        }

        /**
         * 列表ajax数据
         */
        public function listsAjax(Request $request)
        {
            $limit = $request->input('limit', 10);
            $title = $request->input('title');
            $type = $request->input('type');

            $where = [];

            if ($title) {
                $where[] = ['title', 'like', '%' . $title . '%'];
            }

            if (isset($type) && $type) {
                $where[] = ['type', '=', $type];
            }

            $result = Push::where($where)
                ->select('id', 'title', 'content', 'type', 'created_at')
                ->orderBy('id', 'desc')
                ->paginate($limit)
                ->toArray();

            if (!$result['data']) {
                return res_error('数据为空');
            }

            foreach ($result['data'] as $k => $v) {
                $result['data'][$k]['type'] = $v['type'] == 1 ? '所有用户' : '指定用户';
            }

            return res_success($result['data'], $result['total']);
        }

        /**
         * 添加/编辑
         */
        public function edit(Request $request)
        {
            if ($request->isMethod('post')) {
                //验证规则
                $validator = Validator::make($request->all(), [
                    'title'   => 'required',
                    'url'     => 'required',
                    'type'    => 'numeric',
                    'content' => 'required',
                ], [
                                                 'title.required'   => '标题不能为空',
                                                 'url.required'     => '跳转地址不能为空',
                                                 'type.numeric'     => '类型只能是数字',
                                                 'content.required' => '内容不能为空',
                                             ]);
                $error = $validator->errors()->all();
                if ($error) {
                    return res_error(current($error));
                }

                $save_data = [];

                foreach ($request->only('title', 'type', 'm_id', 'url', 'content', 'm_type') as $key => $value) {
                    $save_data[$key] = ($value || $value == 0) ? $value : '';
                }

                //指定标签推送
                if ($request->type == 3) {
                    $save_data['m_type'] = join(',', $save_data['m_type']);
                    $save_data['m_id'] = '';
                } else {
                    $save_data['m_type'] = '';
                }

                if ($request->id) {
                    $res = Push::where('id', $request->id)->update($save_data);
                } else {
                    $res = Push::create($save_data);
                }

                if ($res) {
                    return res_success();
                } else {
                    return res_error('保存失败');
                }

            } else {
                $item = [];
                if ($request->id) {
                    $item = Push::find($request->id);
                    if (!$item) {
                        return res_error('数据错误');
                    }
                    $item['m_type[]'] = str_replace(',', ';', $item['m_type']);
                }

                return view('admin.system.push.edit', ['item' => $item]);
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
            $ids = [];
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

            $res = Push::whereIn('id', $ids)->delete();
            if ($res) {
                return res_success();
            } else {
                return res_error('删除失败');
            }
        }

        /**
         * 推送
         */
        public function push(Request $request)
        {
            $push_id = $request->post('id');
            if (!$push_id) {
                return res_error('缺少id');
            }
            $push_info = Push::find($push_id);
            if (!$push_info) {
                return res_error('无效的id');
            }

            if ($push_info['type'] == 1) {
                //所有用户
                try {
                    $ios_predefined = [
                        'alert'       => ['title' => $push_info['title'], 'body' => $push_info['content']],
                        'description' => $push_info['content'],
                        'alias_type'  => 'ios',
                    ];
                    $android_predefined = [
                        'ticker'      => $push_info['title'],
                        'title'       => $push_info['title'],
                        'description' => $push_info['content'],
                        'alias_type'  => 'android',
                        'text'        => $push_info['content'],
                    ];
                    $url = $push_info['url'];
                    if ($url != 'go_app' && !empty($url)) {
                        $after_open = 'go_url';
                    } else {
                        $after_open = 'go_app';
                    }
                    $android_predefined['after_open'] = $after_open;

                    PushService::iosSendBroadcast($ios_predefined);
                    PushService::androidSendBroadcast($android_predefined);

                    return res_success('', '', '推送成功');
                } catch (\Exception $e) {
                    $error = [
                        'code' => $e->getCode(),
                        'msg'  => $e->getMessage(),
                    ];

                    return res_error($error);
                }
            } else if ($push_info['type'] == 2) {
                //指定用户
                try {
                    $ios_predefined = [
                        'alert'       => ['title' => $push_info['title'], 'body' => $push_info['content']],
                        'description' => $push_info['content'],
                        'alias_type'  => 'ios',
                        'alias'       => $push_info['m_id'],
                        'badge'       => 0,
                        'sound'       => 'chime',
                    ];
                    $android_predefined = [
                        'title'       => $push_info['title'],
                        'description' => $push_info['content'],
                        'alias_type'  => 'android',
                        'alias'       => $push_info['m_id'],
                        'text'        => $push_info['content'],
                        'ticker'      => $push_info['title'],
                    ];
                    $url = $push_info['url'];
                    if ($url != 'go_app' && !empty($url)) {
                        $after_open = 'go_url';
                    } else {
                        $after_open = 'go_app';
                    }
                    $android_predefined['after_open'] = $after_open;

                    PushService::iosSendCustomizedcast($push_info['m_id'], 'ios', $ios_predefined);
                    PushService::androidSendCustomizedcast($push_info['m_id'], 'android', $android_predefined);

                    return res_success('', '', '推送成功');
                } catch (\Exception $e) {
                    $error = [
                        'code' => $e->getCode(),
                        'msg'  => $e->getMessage(),
                    ];

                    return res_error($error);
                }
            } else {
                //标签推送
                try {
                    $ios_predefined = [
                        'alert'       => ['title' => $push_info['title'], 'body' => $push_info['content']],
                        'description' => $push_info['content'],
                        'badge'       => 0,
                        'sound'       => 'chime',
                    ];
                    $android_predefined = [
                        'title'       => $push_info['title'],
                        'description' => $push_info['content'],
                        'text'        => $push_info['content'],
                        'ticker'      => $push_info['title'],
                    ];
                    $url = $push_info['url'];
                    if ($url != 'go_app' && !empty($url)) {
                        $after_open = 'go_url';
                    } else {
                        $after_open = 'go_app';
                    }
                    $android_predefined['after_open'] = $after_open;

                    $umeng_tag = [];
                    foreach (explode(',', $push_info['m_type']) as $m_type) {
                        $umeng_tag[] = ['tag' => 'type_' . $m_type];
                    }
                    $filter = [
                        "where" => [
                            "and" => [
                                [
                                    "or" => $umeng_tag,
                                ],
                            ],
                        ],
                    ];

                    PushService::iosSendGroupcast($filter, $ios_predefined,[]);
                    PushService::androidSendGroupcast($filter, $android_predefined,[]);

                    return res_success('', '', '推送成功');
                } catch (\Exception $e) {
                    $error = [
                        'code' => $e->getCode(),
                        'msg'  => $e->getMessage(),
                    ];

                    return res_error($error);
                }
            }
        }

    }
