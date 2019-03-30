<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/4/1
 * Time: 下午2:58
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

/**
 * api接口验证签名和token
 * Class CheckSignToken
 * @package App\Http\Middleware
 */
class CheckSignToken
{
    public function handle($request, Closure $next) {
        $post_data = $request->post();
        //self::checkSign($post_data);//签名验证
        self::checkToken($request->post());//token验证
        return $next($request);
    }

    /**
     * 验证token和设备
     * @param $post_data
     * @return array
     */
    private function checkToken($post_data) {
        $platform = get_platform();
        //微信公众号和h5采用session验证
        if (in_array($platform, array('wx', 'h5'))) {
            $api_token = session('api_token');
        } else {
            $api_token = auth('api')->getTokenForRequest();
        }
        if (!$api_token) {
            api_error(__('api.invalid_token'));
        }
        $device = Redis::get('api_token:' . $api_token);
        if (!$device || $device != get_device()) {
            api_error(__('api.invalid_device'));
        }
        if (!auth('api')->check()) {
            api_error(__('api.invalid_session'));
        }
    }

    /**
     * 验证签名
     * @param $post_data 所有post过来的数据
     * @return array
     */
    private function checkSign($post_data) {
        if (!$post_data) {
            api_error(__('api.missing_params'));
        }
        if (!isset($post_data['timestamp']) || !$post_data['timestamp']) {
            api_error(__('api.timestamp_error'));
        }
        if ($post_data['timestamp'] - time() > 60) {
            api_error(__('api.timestamp_out'));
        }
        //除去待签名参数数组中的空值和签名参数
        $data_filter = $this->array_filter($post_data);
        //生成签名结果
        $mysign = $this->build_sign($data_filter);
        if (!isset($post_data['sign']) || $mysign != $post_data['sign']) {
            api_error(__('api.invalid_sign'));
        }
    }

    /**
     * 除去数组中的空值和签名参数
     * @param $post_data post数据
     * @return array
     */
    private function array_filter($post_data) {
        $para_filter = array();
        foreach ($post_data as $key => $val) {
            if ($key == "sign" || $val == "") {
                continue;
            } else {
                $para_filter[$key] = $post_data[$key];
            }
        }
        ksort($para_filter);
        reset($para_filter);
        return $para_filter;
    }

    /**
     * 生成签名结果
     * @param $data_filter 参与签名的数据
     * @return string
     */
    private function build_sign($data_filter) {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->create_linkstring($data_filter);
        //把拼接后的字符串再与安全校验码直接连接起来
        $prestr = $prestr . '&key=' . config('app.api_key');
        //把最终的字符串签名，获得签名结果
        $mysgin = md5($prestr);
        return strtoupper($mysgin);
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $data_filter 要拼接的数据
     * @return string
     */
    private function create_linkstring($data_filter) {
        $arg = "";
        foreach ($data_filter as $key => $val) {
            $arg .= $key . "=" . $val . "&";
        }
        //去掉最后一个&字符
        $arg = trim($arg, '&');
        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;
    }
}