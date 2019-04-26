<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
class WechatController extends Controller
{
    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
//        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        $app = app('wechat.official_account');
//        $app->server->push(function($message){
//            return "欢迎关注 overtrue！";
//        });


        $app->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'event':
                    return '欢迎关注我的订阅号';
                    break;
                case 'text':
                    return '我收到了你发的：'.$message['Content'];
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                case 'file':
                    return '收到文件消息';
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }

            // ...
        });


        return $app->server->serve();
    }
}