<?php
    /**
     * Created by PhpStorm.
     * User: wanghui
     * Date: 2018/4/1
     * Time: 下午4:03
     */

    use App\Models\BalanceRecharge;
    use App\Models\MeibiRecharge;


    /**
     * 接口错误返回
     * @param string $error_info 错误信息
     * return array
     */
    function api_error($error_info = '')
    {
        if (!$error_info) {
            $error_info = '无效的请求';
        }
        throw new \App\Exceptions\ApiException($error_info);
    }

    /**
     * 获取设备号
     * @return mixed
     */
    function get_device()
    {
        $device = request()->cookie('device');
        if (!$device) {
            $device = request()->device;
            if (!$device) {
                $device = session('device');
            }
        }

        return $device;
    }


    /**
     * 获取idfa
     * @return mixed
     */
    function get_idfa()
    {
        $idfa = request()->cookie('idfa');
        if (!$idfa) {
            $idfa = request()->idfa;
            if (!$idfa) {
                $idfa = session('idfa');
            }
        }

        return $idfa;
    }


    /**
     * 获取平台类型
     * @return mixed
     */
    function get_platform()
    {
        //web网页，h5移动端网页，mp微信，wechat小程序，ios，android
        $platform = request()->cookie('platform');
        if (!$platform) {
            $platform = request()->platform;
            if (!$platform) {
                $platform = session('platform');
            }
        }

        return strtolower($platform);
    }

    /**
     * 获取手机型号
     * @return mixed
     */
    function get_mobile_model()
    {
        $mobile_model = request()->cookie('mobile_model');
        if (!$mobile_model) {
            $mobile_model = request()->mobile_model;
            if (!$mobile_model) {
                $mobile_model = session('mobile_model');
            }
        }

        return strtolower($mobile_model);
    }

    /**
     * 用户头像
     * @param $headimg
     * @return string
     */
    function get_headimg($headimg = '')
    {
        return $headimg ? $headimg : 'https://video.hipaygo.me/user.jpg';
    }


    /**
     * 获取等比缩放的商品图片
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    function resize_image($image_url, $size = 0)
    {
        $image = $size ? $image_url . '?x-oss-process=image/resize,l_' . $size : $image_url;

        return $image;
    }


    /**
     * 等比缩放图片，限定在矩形框内
     * $w=宽，$h=高
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    function resize_images($image_url, $w = 0, $h = 0)
    {
        if (strpos($image_url, 'http') === false) {
            return $image_url;
        } else {
            $image = $w ? $image_url . '?x-oss-process=image/resize,m_lfit,' . 'h_' . $h . ',w_' . $w : $image_url;

            return $image;
        }
    }

    /**
     * 裁剪图从起点(100, 50)到图的结束
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    function crop_image($image_url, $x = 0, $y = 0)
    {
        $image = $x ? $image_url . '?x-oss-process=image/crop,' . 'x_' . $x . ',y_' . $y : $image_url;

        return $image;
    }

    /**
     * 设置memcache缓存
     * @return string
     */
    function set_memcache($cacheKey, $value, $time = 10)
    {
        $cacheData = \Illuminate\Support\Facades\Cache::get($cacheKey);
        if (!$cacheData) {
            \Illuminate\Support\Facades\Cache::add($cacheKey, $value, $time);
        } else {
            \Illuminate\Support\Facades\Cache::put($cacheKey, $value, $time);
        }
    }

    /**
     * 获取memcache缓存
     * @return string
     */
    function get_memcache($cacheKey)
    {
        if ($cacheKey) {
            $cacheData = \Illuminate\Support\Facades\Cache::get($cacheKey);

            return ($cacheData);
        }

        return false;

    }

    /**
     * 删除memcache缓存
     * @return string
     */
    function delete_memcache($cacheKey)
    {
        if ($cacheKey) {
            $cacheData = \Illuminate\Support\Facades\Cache::forget($cacheKey);

            return true;
        }

        return false;

    }

    /**
     * 获取分页信息
     * @return mixed
     */
    function get_page_params()
    {
        $page = (int)request()->page;
        $pagesize = (int)request()->pagesize;
        if (!$page) $page = 1;
        if (!$pagesize) $pagesize = 20;
        if ($pagesize > 50) {
            $pagesize = 50;
        }
        $offset = $pagesize * ($page - 1);

        return [$page, $pagesize, $offset];
    }


