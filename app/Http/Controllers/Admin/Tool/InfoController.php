<?php

namespace App\Http\Controllers\Admin\Tool;

use App\Models\Info;
use App\Models\InfoComment;
use App\Models\InfoImage;
use App\Models\InfoRecord;
use App\Service\PushService;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InfoController extends Controller
{
    public function lists()
    {
        return view('admin.tool.info.lists');
    }

    /**
     * 列表ajax数据
     * @param Request $request
     * create by: liuqi
     * Date:2018年7月3日14:43:52
     */
    public function listsAjax(Request $request)
    {
        $limit   = $request->input('limit', 10);
        $keyword = $request->input('keyword');

        $where = [];
        if ($keyword) {
            $where[] = ['title', 'like', '%' . $keyword . '%'];
        }

        $result = Info::select('id', 'title', 'url', 'created_at', 'status', 'lang', 'position')
            ->where($where)
            ->orderBy('id', 'desc')
            ->paginate($limit)->toArray();

        if (!$result['data']) {
            return res_error('数据为空');
        }

        $info_ids = [];
        foreach ($result['data'] as $key => $value) {
            $info_ids[] = $value['id'];
        }

        $img  = InfoImage::whereIn('info_id', $info_ids)->select('image', 'info_id')->get()->toArray();
        $imgs = [];
        if ($img) {
            foreach ($img as $ik => $iv) {
                $imgs[ $iv['info_id'] ][] = $iv['image'];
            }
        }
        foreach ($result['data'] as $key => $value) {
            $result['data'][ $key ]['image'] = isset($imgs[ $value['id'] ]) ? $imgs[ $value['id'] ] : '';
        }

        return res_success($result['data'], $result['total']);
    }

    /**
     * 添加/编辑
     * @param Request $request
     * create by: liuqi
     * Date:
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('post')) {
            //验证规则
            $validator = Validator::make($request->all(), [
                'title'    => 'required',
                'image'    => 'required',
                'lang'     => 'numeric',
                'position' => 'numeric',
                'url'      => 'url',
            ], [
                'title.required'   => '标题不能为空',
                'image.required'   => '缩略图不能为空',
                'lang.numeric'     => '请选择正确的语言',
                'position.numeric' => '排序只能是数字',
                'url.url'          => '请填写正确的h5地址',
            ]);

            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = [];
            foreach ($request->only(['title', 'lang', 'position', 'url']) as $key => $val) {
                $save_data[ $key ] = ($val || $val == 0) ? $val : '';
            }

            $images = $request->image ? $request->image : [];


            try {
                $res = DB::transaction(function () use ($request, $save_data, $images) {
                    if ($request->id) {
                        $res  = Info::where('id', $request->id)->update($save_data);
                        $del  = InfoImage::where('info_id', $request->id)->delete();
                        $nums = count($images);
                        if ($nums > 0) {
                            for ($i = 0; $i < $nums; $i++) {
                                $add = InfoImage::create(['info_id' => $request->id, 'image' => $images[ $i ]]);
                            }
                        }
                    } else {
                        $save_data['created_at'] = date('Y-m-d H:i:s');
                        $save_data['updated_at'] = date('Y-m-d H:i:s');
                        $res                     = Info::insertGetId($save_data);
                        $nums                    = count($images);
                        if ($nums > 0) {
                            for ($i = 0; $i < $nums; $i++) {
                                $add = InfoImage::create(['info_id' => $res, 'image' => $images[ $i ]]);
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
            $item = $lang = [];
            $lang = config('app.config_lang');
            if ($request->id) {
                $item = Info::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
            }
            $img                 = InfoImage::where('info_id', $request->id)->pluck('image')->toArray();
            $item['goods_image'] = $img;

            return view('admin.tool.info.edit', ['item' => $item, 'lang' => $lang]);
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
            $res = Info::whereIn('id', $ids)->update(['status' => $status]);
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

        try {
            $res = DB::transaction(function () use ($request, $ids) {
                $del_res = Info::whereIn('id', $ids)->delete();
                $del     = InfoImage::whereIn('info_id', $ids)->delete();

                return $del;
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
    public function position(Request $request)
    {
        $id       = (int)$request->id;
        $position = (int)$request->position;
        if ($id && isset($position)) {
            $res = Info::where('id', $id)->update(['position' => $position]);
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
     * 推送
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function push(Request $request)
    {
        $id = (int)$request->id;
        if ($id) {
            //type 1:资讯 2:直播
            $article_data   = Info::find($id);
            $support_column = InfoRecord::where('info_id', $id)->count();
            $comment_column = InfoComment::where('info_id', $id)->count();
            $info_id        = $article_data['id'];
            $time           = json_encode($article_data['created_at']);
            $time           = (json_decode($time, true));
            $custom         = ['info_id' => "$info_id", 'title' => $article_data['title'], 'url' => $article_data['url']
                , 'support_num'          => "$support_column", 'comment_num' => "$comment_column"
                , 'created_at'           => date('Y-m-d', strtotime($time['date']))
                , 'have_support'         => 0
                , 'type'                 => '1'];
            //所有用户
            try {
                $ios_predefined     = [
                    'alert'       => $custom,
                    'description' => '发布了新文章',
                    'alias_type'  => 'ios',
                ];
                $android_predefined = [
                    'ticker'      => '发布了新文章',
                    'title'       => $article_data['title'],
                    'description' => $article_data['url'],
                    'alias_type'  => 'android',
                    'text'        => '新闻',
                    'custom'      => $custom,
                    'after_open'  => 'go_custom'
                ];

                //PushService::iosSendCustomizedcast('1354', 'ios', $ios_predefined);
                //PushService::androidSendCustomizedcast('1354', 'android', $android_predefined);

                PushService::iosSendCustomizedcast('6207', 'ios', $ios_predefined);
                PushService::androidSendCustomizedcast('6207', 'android', $android_predefined);

                //PushService::iosSendCustomizedcast('6192', 'ios', $ios_predefined);
                //PushService::androidSendCustomizedcast('6192', 'android', $android_predefined);

                //PushService::iosSendCustomizedcast('1543', 'ios', $ios_predefined);
                //PushService::androidSendCustomizedcast('1543', 'android', $android_predefined);

                //PushService::iosSendBroadcast($ios_predefined);
                // PushService::androidSendBroadcast($android_predefined);
                return res_success('', '', '推送成功');
            } catch (\Exception $e) {
                $error = [
                    'code' => $e->getCode(),
                    'msg'  => $e->getMessage(),
                ];

                return res_error($error);
            }
        } else {
            return res_error('参数错误');
        }
    }
}
