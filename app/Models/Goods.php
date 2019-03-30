<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/8
 * Time: 下午5:11
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * 商品
 * Class Goods
 * @package App\Models
 */
class Goods extends Model
{
    use SoftDeletes;
    //状态
    const STATUS_OFF = 0;
    const STATUS_ON = 1;

    const STATUS_DESC = [
        self::STATUS_ON => '正常',
        self::STATUS_OFF => '待审'
    ];

    //是否推荐
    const REM_OFF = 0;
    const REM_ON = 1;

    const REM_DESC = [
        self::REM_ON => '推荐',
        self::REM_OFF => '不推荐'
    ];

    //商品类型
    const TYPE_GOODS = 1;
    const TYPE_POINT = 2;

    const TYPE_DESC = [
        self::TYPE_GOODS => '普通商品',
        self::TYPE_POINT => '积分商品'
    ];

    protected $table = 'goods';
    protected $guarded = ['id'];

    /**
     * 获取详情
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function goodsDesc() {
        return $this->hasOne('App\Models\GoodsDesc');
    }

    /**
     * 获取商品图片
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goodsImage() {
        return $this->hasMany('App\Models\GoodsImage');
    }
    

    /**
     * 获取商品分类
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goodsCategory() {
        return $this->hasMany('App\Models\GoodsCategory');
    }

    /**
     * 获取商品属性
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goodsAttr() {
        return $this->hasMany('App\Models\GoodsAttr');
    }

    /**
     * 获取子商品
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goodsSku() {
        return $this->hasMany('App\Models\GoodsSku');
    }

    /**
     * 添加商品
     * @param $goods_data
     * @param string $id
     * @return bool
     */
    public static function addGoods($goods_data, $id = '') {
        try {
            DB::transaction(function () use ($goods_data, $id){
                //修改主商品
                if ($id) {
                    self::where('id', $id)->update($goods_data['goods']);
                    GoodsDesc::where('goods_id', $id)->update(['desc' => $goods_data['desc']]);
                    GoodsCategory::where('goods_id', $id)->delete();
                    GoodsImage::where('goods_id', $id)->delete();
                    GoodsAttr::where('goods_id', $id)->delete();
                    GoodsSku::where('goods_id', $id)->update(['status' => GoodsSku::STATUS_DEL]);
                } else {
                    $result = self::create($goods_data['goods']);
                    $id = $result->id;
                    GoodsDesc::create(['goods_id' => $id, 'desc' => $goods_data['desc']]);
                }

                //商品图片
                if (isset($goods_data['goods_image']) && $goods_data['goods_image']) {
                    $goods_image = array();
                    foreach ($goods_data['goods_image'] as $value) {
                        $_item = array(
                            'goods_id' => $id,
                            'image' => $value
                        );
                        $goods_image[] = $_item;
                    }
                    GoodsImage::insert($goods_image);
                }
                //sku商品
                if (isset($goods_data['goods_sku']) && $goods_data['goods_sku']) {
                    foreach ($goods_data['goods_sku'] as $value) {
                        $sku_id = $value['sku_id'];
                        unset($value['sku_id']);
                        $value['goods_id'] = $id;
                        $value['status'] = GoodsSku::STATUS_ON;
                        if ($sku_id) {
                            GoodsSku::where('id', $sku_id)->update($value);
                        } else {
                            GoodsSku::create($value);
                        }
                    }
                }
                //商品属性
                if (isset($goods_data['attribute']) && $goods_data['attribute']) {
                    $goods_attr = $goods_data['attribute'];
                    foreach ($goods_data['attribute'] as $key => $value) {
                        $value['goods_id'] = $id;
                        $goods_attr[$key] = $value;
                    }
                    GoodsAttr::insert($goods_attr);
                }
                GoodsCategory::insert(['goods_id' => $id, 'category_id' => $goods_data['category_id']]);
            });
            return true;
        } catch (\Exception $e) {
            dd($e);
            return false;
        }
    }

    /**
     * 指定分类下的所有商品
     */
    public static function getOneCateGoods($category_id) {
        $where=[];
        if(!is_null($category_id)){
            if($category_id==0){
                $where=[];
            }else{
                $where=array('category_id'=>$category_id);
            }

        }
        $category_ids = GoodsCategory::select('goods_id')->where($where)->get();
        if ($category_ids->isEmpty()) {
            api_error(__('api.content_is_empty'));
        }
        $category_ids->toArray();
        foreach ($category_ids as $v) {
            $goods_ids[] = $v['goods_id'];
        }
        return $goods_ids;
    }
    
}