<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/15
 * Time: 下午1:09
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Areas;
use Illuminate\Http\Request;

/**
 * 地区管理
 * Class AreaController
 * @package App\Http\Controllers\Admin
 */
class AreaController extends Controller
{
    /**
     * 根据上级id获取下级地区列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        $parent_id = (int)$request->parent_id;
        $area = Areas::where('parent_id', $parent_id)
            ->select('id', 'name')
            ->orderBy('id', 'asc')
            ->get()->toArray();
        return res_success($area);
    }
}