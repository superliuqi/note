<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/10
 * Time: 下午1:42
 */

namespace App\Libs\Tencent;

/**
 * 腾讯云im
 * Class Upload
 * @package App\Libs
 */
class TencentIm
{

    #app基本信息
    protected $sdkappid = 0;
    protected $usersig = '';
    protected $identifier = '';

    #开放IM https接口参数, 一般不需要修改
    protected $http_type = 'https://';
    protected $method = 'post';
    protected $im_yun_url = 'console.tim.qq.com';
    protected $version = 'v4';
    protected $contenttype = 'json';
    protected $apn = '0';

    public function __construct() {
        $this->sdkappid = config('tencent.im.appid');
        $this->identifier = config('tencent.im.identifier');
        $this->private_key_path = dirname(__FILE__) . '/im_sdk/keys/private_key';
        $this->signature = dirname(__FILE__) . '/im_sdk/signature/linux-signature64';
    }

    /**
     * 派发用户签名
     * @param $identifier 用户名
     * @return string
     */
    public function userSig($identifier = '') {
        if (!$identifier) $identifier = $this->identifier;
        $command = escapeshellarg($this->signature)
            . ' ' . escapeshellarg($this->private_key_path)
            . ' ' . escapeshellarg($this->sdkappid)
            . ' ' . escapeshellarg($identifier) . ' 2>&1';
        $ret = exec($command, $out, $status);
        if ($status == -1) {
            return false;
        }
        $this->usersig = $out[0];
        return $out[0];
    }

    /**
     * 同步用户到腾讯
     * @param $identifier 用户名
     * @param $nick_name 昵称
     * @param $headimg 头像
     * @return string
     */
    public function accountImport($identifier, $nick_name, $headimg) {
        $msg = array(
            'Identifier' => $identifier,
            'Nick' => $nick_name,
            'FaceUrl' => $headimg,
        );
        $res = $this->api("im_open_login_svc", "account_import", $msg);
        if ($res['ActionStatus'] == 'OK') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 创建聊天室
     * @param $group_name 群组名称
     * @param $group_type 群组类型
     * @return string
     */
    public function createRoom($group_name, $group_type) {
        $msg = array(
            'Type' => $group_type,
            'Name' => $group_name,
            'Owner_Account' => null,
            'ApplyJoinOption' => 'FreeAccess',
            'Introduction' => null,
            'Notification' => null
        );
        $res = $this->api("group_open_http_svc", "create_group", $msg);
        if ($res['ActionStatus'] == 'OK') {
            return $res['GroupId'];
        } else {
            return false;
        }
    }

    /**
     * 注销聊天室
     * @param $group_id 群组id
     * @return string
     */
    public function removeRoom($group_id) {
        $msg = array(
            "GroupId" => $group_id,
        );
        $res = $this->api("group_open_http_svc", "destroy_group", $msg);
        if ($res['ActionStatus'] == 'OK') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 发送自定义消息到聊天室
     * @param $group_id 群组id
     * @param $data 发送数据
     * @param $account_id 发送用户id
     * @return string
     */
    public function sendGroupCustomMsg($group_id, $cmd_type, $data, $account_id = '') {
        $msg_content[] = array(
            'MsgType' => 'TIMCustomElem',//自定义消息
            'MsgContent' => array(
                'Data' => json_encode([
                    'data' => $data,
                    'cmd_type' => $cmd_type
                ]),
                'Desc' => '收到一条新消息'
            )
        );
        $res = $this->sendGroupMsg($group_id, $msg_content, $account_id);
        if ($res['ActionStatus'] == 'OK') {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 发送普通消息到聊天室
     * @param $group_id 群组id
     * @return string
     */
    public function sendGroupMsg($group_id, $msg_content, $account_id = '') {
        $msg = array(
            "GroupId" => $group_id,
            "Random" => rand(1, 65535),
            "MsgBody" => $msg_content
        );
        if ($account_id) $msg['From_Account'] = $account_id;
        $res = $this->api("group_open_http_svc", "send_group_msg", $msg);
        return $res;
    }


    /**
     * 发送系统消息到聊天室
     * @param $group_id 群组id
     * @return string
     */
    public function sendSystemMsg($group_id, $data, $cmd_type)
    {
        $msg_content = [
            'data'     => $data,
            'cmd_type' => $cmd_type,
        ];
        $msg = [
            "GroupId" => $group_id,
            "Content" => json_encode($msg_content),
        ];
        $res = $this->api("group_open_http_svc", "send_group_system_notification", $msg);

        return $res;
    }

    /**
     * 发送单聊消息到
     * @param $group_id 群组id
     * @return string
     */
    public function sendMsgToOne($data = '', $cmd_type, $To_Account, $SyncOtherMachine = 2, $From_Account = '', $MsgLifeTime = 60)
    {
        $msg_content[] = [
            'MsgType'    => 'TIMCustomElem',//自定义消息
            'MsgContent' => [
                'Data' => json_encode([
                                          'data'     => $data,
                                          'cmd_type' => $cmd_type,
                                      ]),
            ],
        ];
        $msg = [
            "SyncOtherMachine" => $SyncOtherMachine,
            "To_Account"       => $To_Account,
            "MsgLifeTime"      => $MsgLifeTime,
            "MsgRandom"        => mt_rand(1, 65535),
            "MsgTimeStamp"     => time(),
            "MsgBody"          => $msg_content,
        ];
        if ($From_Account) {
            $msg['From_Account'] = $From_Account;
        }
        $res = $this->api("openim", "sendmsg", $msg);
        return $res;
    }


    /**
     * 聊天室禁言
     * @param $group_id 群组id
     * @param $account_id array 用户
     * @param $time 禁言时间
     * @return string
     */
    public function setShutUp($group_id, $account_id = array(), $time = 3600) {
        $msg = array(
            "GroupId" => $group_id,
            "Members_Account" => $account_id,
            "ShutUpTime" => $time
        );
        $res = $this->api("group_open_http_svc", "forbid_send_msg", $msg);
        return $res;
    }

    /**
     * 构造访问REST服务器的参数,并访问REST接口
     * @param string $server_name 服务名
     * @param string $cmd_name 命令名
     * @param string $req_data 传递的json结构
     * $param bool $print_flag 是否打印请求，默认为打印
     * @return string $out 返回的签名字符串
     */
    public function api($service_name, $cmd_name, $req_data, $print_flag = true) {
        $req_data = json_encode($req_data);
        $usersig = $this->userSig($this->identifier);
        $parameter = "usersig=" . $usersig
            . "&identifier=" . $this->identifier
            . "&sdkappid=" . $this->sdkappid
            . "&contenttype=" . $this->contenttype;
        $url = $this->http_type . $this->im_yun_url . '/' . $this->version . '/' . $service_name . '/' . $cmd_name . '?' . $parameter;
        /*if ($print_flag) {
            echo "Request Url:\n";
            echo $url;
            echo "\n";
            echo "Request Body:\n";
            echo $req_data;
            echo "\n";
        }*/
        $ret = $this->http_req('https', 'post', $url, $req_data);
        $ret = json_decode($ret, true);
        return $ret;

    }

    /**
     * 向Rest服务器发送请求
     * @param string $http_type http类型,比如https
     * @param string $method 请求方式，比如POST
     * @param string $url 请求的url
     * @return string $data 请求的数据
     */
    public static function http_req($http_type, $method, $url, $data) {
        $ch = curl_init();
        if (strstr($http_type, 'https')) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        if ($method == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            $url = $url . '?' . $data;
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100000);//超时时间

        try {
            $ret = curl_exec($ch);
        } catch (Exception $e) {
            curl_close($ch);
            return json_encode(array('ret' => 0, 'msg' => 'failure'));
        }
        curl_close($ch);
        return $ret;
    }
}