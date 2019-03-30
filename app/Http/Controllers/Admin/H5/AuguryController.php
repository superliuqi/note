<?php
    /**
     * Created by PhpStorm.
     * User: wanghui
     * Date: 2018/5/14
     * Time: 下午1:28
     */

    namespace App\Http\Controllers\Admin\h5;

    use App\Http\Controllers\Controller;
    use App\Models\H5\H5Augury;
    use Validator;
    use Illuminate\Http\Request;

    /**
     * H5-占卜管理
     * Class MemberController
     * @package App\Http\Controllers\Admin\Member
     */
    class AuguryController extends Controller
    {
        /**
         * 列表
         * @param Request $request
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
         */
        public function lists(Request $request)
        {
            return view('admin.h5.augury.lists');
        }


        /**
         * 列表ajax数据
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function listsAjax(Request $request)
        {
            $limit = $request->input('limit', 10);

            //搜索
            $where = [];

            $result = H5Augury::select('*')
                ->where($where)
                ->orderBy('id', 'desc')
                ->paginate($limit)->toArray();
            if (!$result['data']) {
                return res_error('数据为空');
            }

            return res_success($result['data'], $result['total']);
        }


        /**
         * 删除数据
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function delete(Request $request)
        {
            $id = $request->id;
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

            $res = H5Augury::whereIn('id', $ids)->delete();
            if ($res) {
                return res_success();
            } else {
                return res_error('删除失败');
            }
        }

    }