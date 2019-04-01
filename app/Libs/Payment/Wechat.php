<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/25
 * Time: 上午10:28
 */

namespace App\Libs\Payment;

/**
 * 微信支付
 * Class Wechat
 * @package App\Libs\Payment
 */
class Wechat
{
    public function __construct() {
        $this->platform = get_platform();
        $this->url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $this->app_id = '';
        $this->mch_id = '';
        $this->api_key = '';
        $this->sslcert_path = '';
        $this->sslkey_path = '';
        $this->notify_url = url('/v1/pay/notify/2/');
        $this->return_url = '';
        $this->init();
    }

    /**
     * 初始化配置
     */
    public function init() {
        if (in_array($this->platform, array('mp', 'web', 'h5'))) {
            $config = config('payment.wechat.mp');
        } elseif (in_array($this->platform, array('wechat'))) {
            $config = config('payment.wechat.wechat');
        } else {
            $config = config('payment.wechat.app');
        }
        $this->app_id = $config['app_id'];
        $this->mch_id = $config['mch_id'];
        $this->api_key = $config['api_key'];
        $this->sslcert_path = $config['sslcert_path'];
        $this->sslkey_path = $config['sslkey_path'];
    }

    /**
     * @param $trade_data
     * @return array
     * @throws WxPayException
     */
    public function payData($trade_data) {
        $trade_type = 'APP';//默认app支付
        $platform = get_platform();
        if (in_array($platform, array('mp', 'wechat'))) {
            $trade_type = 'JSAPI';
        } elseif (in_array($platform, array('web'))) {
            $trade_type = 'NATIVE';
        } elseif (in_array($platform, array('h5'))) {
            $trade_type = 'MWEB';
        }
        $weixin_data = $this->unifiedOrder($trade_data, $trade_type);

        $pay_data = array();
        switch ($trade_type) {
            case 'JSAPI':
                $pay_data = array(
                    'appId' => $this->app_id,
                    'timeStamp' => time(),
                    'nonceStr' => str_random(20),
                    'package' => 'prepay_id=' . $weixin_data['prepay_id'],
                    'signType' => 'MD5'
                );
                $pay_data['paySign'] = $this->getSign($pay_data);
                break;
            case 'NATIVE':
                $pay_data['code_url'] = $weixin_data['code_url'];
                break;
            case 'MWEB':
                $pay_data['mweb_url'] = $weixin_data['mweb_url'];
                break;
            default:
                $pay_data = array(
                    'appid' => $this->app_id,
                    'partnerid' => $this->mch_id,
                    'prepayid' => $weixin_data['prepay_id'],
                    'package' => 'Sign=WXPay',
                    'noncestr' => str_random(20),
                    'timestamp' => time()
                );
                $pay_data['sign'] = $this->getSign($pay_data);
                break;
        }
        $pay_data['trade_no'] = $trade_data['trade_no'];
        return $pay_data;
    }

    /**
     * 微信支付统一下单
     * @param $order_data 订单信息
     * @param $trade_type 支付类型
     * @return mixed
     * @throws \App\Exceptions\ApiException
     */
    public function unifiedOrder($trade_data, $trade_type) {
        if (!$trade_data['title'] || !$trade_data['trade_no'] || !$trade_data['amount']) {
            api_error(__('api.missing_params'));
        }

        $pay_data = array(
            'appid' => $this->app_id,
            'mch_id' => $this->mch_id,
            'nonce_str' => str_random(20),
            'body' => $trade_data['title'],
            'out_trade_no' => $trade_data['trade_no'],
            'total_fee' => $trade_data['amount'] * 100,
            'notify_url' => $this->notify_url,
            'trade_type' => $trade_type,
        );
        //公众和小程序支付需要提供openid
        if ($trade_type == 'JSAPI') {
            $openid = get_device();
            if (!$openid) {
                api_error(__('api.pay_openid_error'));
            }
            $pay_data['openid'] = $openid;
        }
        $pay_data['sign'] = $this->getSign($pay_data);
        $xml = $this->toXml($pay_data);
        $response = $this->postXmlCurl($xml, $this->url, false, 6);

        $res = $this->checkReturn($response);
        return $res;
    }

    /**
     * 验证返回参数
     * @param $response
     * @return mixed
     * @throws \App\Exceptions\ApiException
     */
    public function checkReturn($response) {
        $res = $this->fromXml($response);
        //返回是否成功
        if ($res['return_code'] != 'SUCCESS') {
            api_error(__('api.pay_result_error') . $res['return_msg']);
        }
        $res_sign = $this->getSign($res);//验证签名
        if ($res_sign != $res['sign']) {
            api_error(__('api.pay_sign_error'));
        }
        //验证接口请求是否正确
        if ($res['result_code'] != 'SUCCESS') {
            api_error(__('api.pay_result_error') . $res['err_code_des']);
        }
        return $res;
    }

    /**
     * 生成签名
     * @param array $data
     * @return 签名
     */
    public function getSign($data) {
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string = $this->toUrlParams($data);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $this->api_key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     * @param array $data
     */
    public function toUrlParams($data) {
        $buff = "";
        foreach ($data as $k => $v) {
            if ($k != "sign" && $v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 将array转为xml
     * @param array $data
     **/
    public function toXml($data) {
        if (!is_array($data)) return false;

        $xml = "<xml>";
        foreach ($data as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     * @param string $xml
     */
    public function fromXml($data) {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xml = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $xml;
    }

    /**
     * 以post方式提交xml到对应的接口url
     * @param string $xml 需要post的xml数据
     * @param string $url url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second url执行超时时间，默认30s
     */
    private function postXmlCurl($xml, $url, $useCert = false, $second = 30) {
        $ch = curl_init();
        $curlVersion = curl_version();
        $ua = "WXPaySDK/0.9 (" . PHP_OS . ") PHP/" . PHP_VERSION . " CURL/" . $curlVersion['version'] . " " . $this->mch_id;

        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格校验
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if ($useCert == true) {
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            //证书文件请放入服务器的非web目录下
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, $this->sslcert_path);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, $this->sslkey_path);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            api_error(__('api.fail'));
        }
    }

    /**
     * 服务端回调验证
     * @return string
     */
    public function notify() {
        $xml_data = file_get_contents('php://input');
        $post_data = $this->fromXml($xml_data);
        if ($post_data['return_code'] != 'SUCCESS') {
            return false;
        }
        $res_sign = $this->getSign($post_data);//验证签名
        if ($res_sign != $post_data['sign']) {
            return false;
        }
        //验证接口请求是否正确
        if ($post_data['result_code'] != 'SUCCESS') {
            return false;
        }
        $return = array(
            'trade_no' => $post_data['out_trade_no'],
            'pay_total' => round($post_data['total_fee'] / 100, 2),
            'payment_no' => $post_data['transaction_id'],
            'payment_id' => 2
        );
        return $return;
    }

    /**
     * 支付成功
     * @return string
     */
    public function success() {
        return 'success';
    }

    /**
     * 支付失败
     * @return string
     */
    public function fail() {
        return 'fail';
    }
}