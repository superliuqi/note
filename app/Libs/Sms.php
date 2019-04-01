<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/9
 * Time: 上午11:14
 */

namespace App\Libs;
use App\Models\SmsLog;
use App\Models\SmsTemplate;

/**
 * 短信发送
 * Class AliyunOss
 * @package App\Libs
 */
class Sms
{

    /**
     * 发送短信
     * @param $data 参数
     * @param $code 模板编号
     * @param $mobile 手机号
     * @return bool|mixed
     */
    public function SendSms($data, $code, $mobile) {
        $content = $this->GetTemplate($data, $code);
        if ($content) {
            $res = $this->chuangLan($content, $mobile);
            //添加发送记录
            $log_data = array(
                'mobile' => $mobile,
                'content' => $content,
                'error_msg' => $res
            );
            SmsLog::create($log_data);
            return $res;
        } else {
            return '模板不存在';
        }
    }

    /**
     * 获取短信模板并组装参数
     * @param $data 参数
     * @param $code 模板编号
     * @return mixed|string
     */
    public function GetTemplate($data, $code) {
        $template = SmsTemplate::where('code', $code)->first();

        $content = '';
        if ($template['content']) {
            $content = $template['content'];
            if (!empty($data) && is_array($data)) {
                $find = $replace = array();
                foreach ($data as $key => $val) {
                    $find[] = '{$' . $key . '}';
                    $replace[] = $val;
                }
                $content = str_replace($find, $replace, $content);
            }
            $content = trim($content);
        }
        return $content;
    }

    /**
     * 发送短信
     * @param $content 短信内容
     * @param $mobile 手机号
     */
    public function chuangLan($content, $mobile) {
        $post_data = array (
            'account'  =>  config('sms.chuanglan.api_account'),
            'password' => config('sms.chuanglan.api_password'),
            'msg' => urlencode($content),
            'phone' => $mobile
        );
        $res = $this->curlPost(config('sms.chuanglan.url'), $post_data);
        return $res;
    }

    /**
     * 通过CURL发送HTTP POST请求
     * @param $url 请求URL
     * @param $post_data 请求参数
     * @return mixed
     */
    private function curlPost($url,$post_data){
        $post_data = json_encode($post_data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8'
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec($ch);
        $result = '';
        if (false == $ret) {
            $result = curl_error($ch);
        } else {
            $rsp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 " . $rsp . " " . curl_error($ch);
            }
        }
        curl_close($ch);
        if ($result) {
            return $result;
        }
        $result = json_decode($ret, true);
        if ($result['code'] == '0') {
            return true;
        } else {
            return $result['errorMsg'];
        }
    }
}