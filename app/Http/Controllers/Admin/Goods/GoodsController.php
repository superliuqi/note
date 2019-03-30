<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/6/4
 * Time: 下午1:20
 */

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Delivery;
use App\Models\Goods;
use App\Models\GoodsCategory;
use App\Models\GoodsSku;
use App\Models\Seller;
use App\Models\Spec;
use App\Models\SpecValue;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;

/**
 * 商品
 * Class GoodsController
 * @package App\Http\Controllers\Admin\System
 */
class GoodsController extends Controller
{
    /**
     * 选择分类
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function SelectCategory(Request $request) {
        $category = Category::getSelectCategory();
        return view('admin.goods.goods.select_category', ['category' => $category]);
    }

    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function lists(Request $request) {
        $category = Category::getSelectCategory();
        $brand = Brand::all()->pluck('title', 'id')->toArray();
        $seller = Seller::all()->pluck('title', 'id')->toArray();
        $return  = array(
            'category' => $category,
            'brand' => $brand,
            'seller' => $seller
        );
        return view('admin.goods.goods.lists', $return);
    }

    /**
     * 列表ajax数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listsAjax(Request $request) {
        $limit = $request->input('limit', 10);
        $keyword = $request->input('keyword');
        $id = (int)$request->input('id');
        $category_id = (int)$request->input('category_id');
        $seller_id = (int)$request->input('seller_id');
        $brand_id = (int)$request->input('brand_id');
        $status = $request->input('status');

        //搜索
        $where = array();
        if ($id) $where[] = array('id', $id);
        if ($keyword) $where[] = array('title', 'like', '%' . $keyword . '%');
        if ($seller_id) $where[] = array('seller_id', $seller_id);
        if ($brand_id) $where[] = array('brand_id', $brand_id);
        if (is_numeric($status)) $where[] = array('status', $status);

        //分类搜索
        $search_category = '';
        if ($category_id) {
            $search_category_res = GoodsCategory::where('category_id', $category_id)->pluck('goods_id');
            if ($search_category_res->isEmpty()) {
                return res_error('数据为空');
            } else {
                $search_category = $search_category_res->toArray();
            }
        }
        $res = Goods::select('id', 'title', 'sku_code', 'image', 'market_price', 'sell_price', 'is_rem', 'position', 'status', 'created_at')
            ->where($where);
        if ($search_category) {
            $res->whereIn('id', $search_category);
        }

        $result = $res->orderBy('id', 'desc')
            ->paginate($limit)->toArray();
        if (!$result['data']) {
            return res_error('数据为空');
        }
        $goods_ids = array();
        foreach ($result['data'] as $value) {
            $goods_ids[] = $value['id'];
        }
        //获取分类名称
        $goods_category_data = array();
        if ($goods_ids) {
            $goods_category_res = GoodsCategory::whereIn('goods_id', $goods_ids)->pluck('category_id', 'goods_id');
            if (!$goods_category_res->isEmpty()) {
                $goods_category = $goods_category_res->toArray();
                $category_res = Category::whereIn('id', array_unique(array_values($goods_category)))->pluck('title', 'id');
                if (!$category_res->isEmpty()) {
                    $category = $category_res->toArray();
                }
                foreach ($goods_category as $goods_id => $category_id) {
                    $goods_category_data[$goods_id] = isset($category[$category_id]) ? $category[$category_id] : '';
                }
            }
        }
        $data_list = array();
        foreach ($result['data'] as $key => $value) {
            $_item = $value;
            $_item['category'] = isset($goods_category_data[$value['id']]) ? $goods_category_data[$value['id']] : '';
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
                'seller_id' => 'required|numeric',
                'delivery_id' => 'required|numeric',
                'brand_id' => 'required|numeric',
                'type' => 'required|numeric',
                'image' => 'required|array',
                'list_image' => 'required',
                'flag_image' => 'required',
                'sku_code' => [
                    'required',
                    Rule::unique('goods')->ignore($request->id)
                ],
                'position' => 'required|numeric',
                'spec_market_price' => 'required|array',
                'spec_sell_price' => 'required|array',
                'spec_stock' => 'required|array',
                'spec_stock[]' => 'numeric',
                'spec_sku_code' => 'required|array',
                'spec_weight' => 'required|array',
                'spec_weight[]' => 'numeric',
            ], [
                'title.required' => '标题不能为空',
                'category_id.required' => '分类id不能为空',
                'category_id.numeric' => '分类id只能是数字',
                'seller_id.required' => '店铺id不能为空',
                'seller_id.numeric' => '店铺id只能是数字',
                'delivery_id.required' => '运费模板不能为空',
                'delivery_id.numeric' => '运费模板只能是数字',
                'brand_id.required' => '排序不能为空',
                'brand_id.numeric' => '排序只能是数字',
                'type.required' => '商品类型不能为空',
                'type.numeric' => '商品类型只能是数字',
                'image.required' => '图片不能为空',
                'image.array' => '图片不能为空',
                'list_image.required' => '列表图片不能为空',
                'flag_image.required' => '推荐图片不能为空',
                'sku_code.required' => '货号不能为空',
                'sku_code.unique' => '货号已经存在',
                'position.required' => '排序不能为空',
                'position.numeric' => '排序只能是数字',
                'spec_market_price.required' => '市场价不能为空',
                'spec_market_price.array' => '市场价参数错误',
                'spec_sell_price.required' => '销售价不能为空',
                'spec_sell_price.array' => '销售价参数错误',
                'spec_stock.required' => '库存不能为空',
                'spec_stock.array' => '库存参数错误',
                'spec_stock[].numeric' => '库存只能是数字',
                'spec_sku_code.required' => '货号不能为空',
                'spec_sku_code.array' => '货号参数错误',
                'spec_weight.required' => '重量不能为空',
                'spec_weight.array' => '重量参数错误',
                'spec_weight[].numeric' => '重量只能是数字',
            ]);
            $error = $validator->errors()->all();
            if ($error) {
                return res_error(current($error));
            }

            $spec_sku_id = $request->spec_sku_id;
            $spec_market_price = $request->spec_market_price;
            $spec_sell_price = $request->spec_sell_price;
            $spec_stock = $request->spec_stock;
            $spec_sku_code = $request->spec_sku_code;
            $spec_weight = $request->spec_weight;

            $save_data = array();
            //主商品信息
            $goods = array(
                'title' => $request->title,
                'sub_title' => $request->sub_title,
                'image' => current($request->image),
                'list_image' => $request->list_image,
                'flag_image' => $request->flag_image,
                'type' => $request->type,
                'sku_code' => $request->sku_code,
                'delivery_id' => $request->delivery_id,
                'seller_id' => $request->seller_id,
                'brand_id' => $request->brand_id,
                'position' => $request->position,
                'market_price' => min($spec_market_price),
                'sell_price' => min($spec_sell_price),
                'shelves_at' => get_date(),
            );

            //子商品信息
            $goods_sku_save_data = array();
            $spec_id = $request->spec_id;
            $spec_name = $request->spec_name;
            $spec_value = $request->spec_value;
            $spec_image = $request->spec_image;
            $spec_alias = $request->spec_alias;

            foreach ($spec_market_price as $key => $value) {
                $sku_spec_value = array();
                $sku_spec_image = current($request->image);
                if (isset($spec_id[$key])) {
                    foreach ($spec_id[$key] as $k => $v) {
                        $_sku_value = array(
                            'id' => $spec_id[$key][$k],
                            'name' => $spec_name[$key][$k],
                            'value' => $spec_value[$key][$k],
                            'image' => $spec_image[$key][$k],
                            'alias' => $spec_alias[$key][$k],
                        );
                        $sku_spec_value[] = $_sku_value;
                        if (isset($spec_image[$key][$k]) && $spec_image[$key][$k]) {
                            $sku_spec_image = $spec_image[$key][$k];
                        }
                    }
                }
                $_sku_item = array(
                    'sku_id' => isset($spec_sku_id[$key]) ? $spec_sku_id[$key] : '',
                    'market_price' => $spec_market_price[$key],
                    'sell_price' => $spec_sell_price[$key],
                    'sku_code' => $spec_sku_code[$key],
                    'stock' => $spec_stock[$key],
                    'weight' => $spec_weight[$key],
                    'spec_value' => json_encode($sku_spec_value, JSON_UNESCAPED_UNICODE),
                    'image' => $sku_spec_image
                );
                $goods_sku_save_data[] = $_sku_item;
            }

            //属性信息
            $attribute = array();
            if ($request->attribute) {
                foreach ($request->attribute as $key => $value) {
                    if (is_array($value)) {
                        $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                    }
                    $_item = array(
                        'attribute_id' => $key,
                        'value' => $value
                    );
                    $attribute[] = $_item;
                }
            }


            $save_data['goods'] = $goods;
            $save_data['goods_sku'] = $goods_sku_save_data;
            $save_data['goods_image'] = $request->image;//图片信息
            $save_data['category_id'] = $request->category_id;
            $save_data['desc'] = $request->desc;
            $save_data['attribute'] = $attribute;

            $res = Goods::addGoods($save_data, $request->id);

            if ($res) {
                return res_success();
            } else {
                return res_error('保存失败');
            }
        } else {
            $item = array();
            $goods_attr = array();
            $goods_spec = array();
            if ($request->id) {
                $id = (int)$request->id;
                $item = Goods::find($id);
                if (!$item) {
                    return res_error('数据错误');
                }
                $desc = $item->goodsDesc;
                $item['desc'] = $desc['desc'];
                $category_id = $item->goodsCategory()->pluck('category_id')->first();
                $item['goods_image'] = $item->goodsImage()->pluck('image')->toArray();//查询商品图片
                //查询商品属性
                $goods_attr_res = $item->goodsAttr()->pluck('value', 'attribute_id');

                if (!$goods_attr_res->isEmpty()) {
                    $goods_attr = $goods_attr_res->toArray();
                }
                $item['goods_attr'] = $goods_attr;
                //查询子商品
                $goods_sku = array();
                $goods_sku_res = $item->goodsSku()->where('status', GoodsSku::STATUS_ON)->get();
                if (!$goods_sku_res->isEmpty()) {
                    foreach ($goods_sku_res as $value) {
                        $_key_arr = array();
                        $spec_value = json_decode($value['spec_value'], true);
                        foreach ($spec_value as $spec) {
                            $_key_arr[] = $spec['id'];
                            $goods_spec[$spec['id']] = array(
                                'value' => $spec['value'],
                                'image' => $spec['image'],
                                'alias' => $spec['alias'],
                            );
                        }
                        $_key = join('|', $_key_arr);
                        if (!$_key) $_key = 'default';
                        $goods_sku[$_key] = array(
                            'spec_sku_id' => $value['id'],
                            'spec_market_price' => $value['market_price'],
                            'spec_sell_price' => $value['sell_price'],
                            'spec_sku_code' => $value['sku_code'],
                            'spec_stock' => $value['stock'],
                            'spec_weight' => $value['weight']
                        );
                    }
                }
                $item['goods_sku'] = $goods_sku;
            } else {
                $item['goods_sku'] = array();
                $category_id = (int)$request->category_id;
            }
            $item['category_id'] = $category_id;
            $item['sku_code'] = time();
            if (!$item['category_id']) {
                return res_error('请先选择分类');
            }

            $seller = Seller::where('status', Seller::STATUS_ON)->get();
            $brand = Brand::where('status', Brand::STATUS_ON)->get();
            $attribute = self::getAttribute($category_id, $goods_attr);
            $spec = self::getSpec($category_id, $goods_spec);
            $view_data = array(
                'item' => $item,
                'seller' => $seller,
                'brand' => $brand,
                'attribute' => $attribute,
                'spec' => $spec,
            );
            return view('admin.goods.goods.edit', $view_data);
        }
    }

    /**
     * 获取分类下的规格并判断是否已经选择
     * @param $category_id 分类id
     * @param $goods_spec 商品的规格信息
     * @return array
     */
    private function getSpec($category_id, $goods_spec) {
        //查询分类下的属性
        $spec = array();
        $spec_res = Spec::where('category_id', $category_id)
            ->select('id', 'title', 'type')
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        if (!$spec_res->isEmpty()) {
            $spec_ids = array();
            foreach ($spec_res->toArray() as $value) {
                $spec_ids[] = $value['id'];
                $spec[$value['id']] = $value;
            }
            if ($spec_ids) {
                $spec_value_res = SpecValue::whereIn('spec_id', $spec_ids)
                    ->select('id', 'value', 'spec_id')
                    ->orderBy('position', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
                if (!$spec_value_res->isEmpty()) {
                    foreach ($spec_value_res->toArray() as $value) {
                        if (isset($spec[$value['spec_id']])) {
                            //判断已经选择的
                            $value['is_checked'] = 0;
                            if (isset($goods_spec[$value['id']]['value']) && $goods_spec[$value['id']]['value'] == $value['value']) {
                                $value['is_checked'] = 1;
                            }
                            $value['alias'] = isset($goods_spec[$value['id']]['alias']) ? $goods_spec[$value['id']]['alias'] : '';
                            $value['image'] = isset($goods_spec[$value['id']]['image']) ? $goods_spec[$value['id']]['image'] : '';
                            $spec[$value['spec_id']]['value'][] = $value;
                        }
                    }
                }
            }
        }
        return $spec;
    }

    /**
     * 获取分类下的属性并判断是否已经选择
     * @param $category_id 分类id
     * @param $goods_attr 商品的属性信息
     * @return array
     */
    private function getAttribute($category_id, $goods_attr) {
        //查询分类下的属性
        $attribute = array();
        $attribute_res = Attribute::where('category_id', $category_id)
            ->select('id', 'title', 'input_type')
            ->orderBy('position', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        if (!$attribute_res->isEmpty()) {
            $attribute_ids = array();
            foreach ($attribute_res->toArray() as $value) {
                $attribute_ids[] = $value['id'];
                if ($value['input_type'] == 'text' && isset($goods_attr[$value['id']])) {
                    $value['value'] = $goods_attr[$value['id']];
                }
                $attribute[$value['id']] = $value;
            }
            if ($attribute_ids) {
                $attribute_value_res = AttributeValue::whereIn('attribute_id', $attribute_ids)
                    ->select('id', 'value', 'attribute_id')
                    ->orderBy('position', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
                if (!$attribute_value_res->isEmpty()) {
                    foreach ($attribute_value_res->toArray() as $value) {
                        if (isset($attribute[$value['attribute_id']])) {
                            $value['is_checked'] = 0;
                            if (isset($goods_attr[$value['attribute_id']])) {
                                switch ($attribute[$value['attribute_id']]['input_type']) {
                                    //判断已经选择的
                                    case 'checkbox':
                                        $attr_value = json_decode($goods_attr[$value['attribute_id']], true);
                                        if (in_array($value['value'], json_decode($goods_attr[$value['attribute_id']], true))) {
                                            $value['is_checked'] = 1;
                                        }
                                        break;
                                    default:
                                        if ($value['value'] == $goods_attr[$value['attribute_id']]) {
                                            $value['is_checked'] = 1;
                                        }
                                        break;
                                }
                            }
                            $attribute[$value['attribute_id']]['value'][] = $value;
                        }
                    }
                }
            }
        }
        return $attribute;
    }

    /**
     * 获取商家运费模板
     * @param $seller_id 商家id
     * @return array
     */
    public function getDelivery(Request $request) {
        $delivery = array();
        $seller_id = (int)$request->seller_id;
        if (!$seller_id) {
            return res_success($delivery);
        }
        $delivery_res = Delivery::where('seller_id', $seller_id)->select('id', 'title')->get();
        if (!$delivery_res->isEmpty()) {
            $delivery = $delivery_res->toArray();
        }
        return res_success($delivery);
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
            $res = Goods::whereIn('id', $ids)->update(['status' => $status]);
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
     * 修改状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function rem(Request $request) {
        $id = $request->id;
        if (is_array($request->id)) {
            foreach ($id as $val) {
                $ids[] = (int)$val;
            }
        } else {
            $ids = array((int)$id);
        }
        $rem = (int)$request->rem;
        if ($ids && isset($rem)) {
            $res = Goods::whereIn('id', $ids)->update(['is_rem' => $rem]);
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

        $res = Goods::whereIn('id', $ids)->delete();
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
            $res = Goods::where('id', $id)->update(['position' => $position]);
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