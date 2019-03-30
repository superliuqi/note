<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午4:28
 */

namespace App\Http\Controllers\Admin\Tool;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleDesc;
use App\Service\PushService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 文章管理
 * Class ArticleController
 * @package App\Http\Controllers\Admin\Tool
 */
class ArticleController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        //查询分类
        $category = ArticleCategory::getSelectCategory();
        return view('admin.tool.article.lists', ['category' => $category]);
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
        $result = Article::select('id', 'title', 'image', 'category_id', 'position', 'created_at', 'status')
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
            $category_res = ArticleCategory::whereIn('id', array_unique($category_ids))->pluck('title', 'id');
            if (!$category_res->isEmpty()) {
                $category = $category_res->toArray();
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
            ], [
                'title.required' => '标题不能为空',
                'category_id.required' => '分类不能为空',
                'category_id.numeric' => '分类只能是数字',
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $save_data = array();
            foreach ($request->only(['title', 'image','url', 'category_id']) as $key => $value) {
                $save_data[$key] = ($value || $value == 0) ? $value : null;
            }

            try {
                $res = DB::transaction(function () use ($request, $save_data) {
                    if ($request->id) {
                        $res = Article::where('id', $request->id)->update($save_data);
                        ArticleDesc::where('article_id', $request->id)->update(['desc' => $request->desc]);
                    } else {
                        $result = Article::create($save_data);
                        $res = $result->id;
                        ArticleDesc::create(['article_id' => $res, 'desc' => $request->desc]);
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
                $item = Article::find($request->id);
                if (!$item) {
                    return res_error('数据错误');
                }
                //详情
                $desc = $item->articleDesc;
                $item['desc'] = $desc['desc'];
            }
            //查询分类
            $category = ArticleCategory::getSelectCategory();
            return view('admin.tool.article.edit', ['item' => $item, 'category' => $category]);
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
            $res = Article::whereIn('id', $ids)->update(['status' => $status]);
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
                $res = Article::whereIn('id', $ids)->delete();
                ArticleDesc::whereIn('article_id', $ids)->delete();
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
            $res = Article::where('id', $id)->update(['position' => $position]);
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
    public function push(Request $request) {
        $id = (int)$request->id;
        if ($id) {
            $article_data=Article::find($id);
            //所有用户
            try {
                $ios_predefined = [
                    'alert'       => ['title' => '发布了新文章', 'body' => $article_data['title']],
                    'description' => $article_data['url'],
                    'alias_type'  => 'ios',
                ];
                $android_predefined = [
                    'ticker'      => '发布了新文章',
                    'title'       => $article_data['title'],
                    'description' => $article_data['url'],
                    'alias_type'  => 'android',
                    'text'        => $article_data['url'],
                ];

                $android_predefined['after_open'] = 'go_url';

                PushService::iosSendCustomizedcast('1354', 'ios', $ios_predefined);
                PushService::androidSendCustomizedcast('1354', 'android', $android_predefined);

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