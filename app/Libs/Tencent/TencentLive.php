<?php
/**
 * Created by PhpStorm.
 * User: wanghui
 * Date: 2018/5/10
 * Time: 下午1:42
 */

namespace App\Libs\Tencent;
use Illuminate\Support\Facades\Redis;

/**
 * 腾讯云直播
 * Class Upload
 * @package App\Libs
 */
class TencentLive
{
    protected $bizid;
    protected $push_key;
    protected $push_url;
    protected $play_url;

    public function __construct() {
        $this->appid = config('tencent.live.appid');
        $this->bizid = config('tencent.live.bizid');
        $this->push_key = config('tencent.live.push_key');
        $this->api_key = config('tencent.live.api_key');
        $this->push_url = config('tencent.live.push_url');
        $this->play_url = config('tencent.live.play_url');
    }

    /**
     * 推流url
     */
    public function pushUrl($stream_id, $live_id, $type = 1) {
        if (!$stream_id || !$live_id) {
            return false;
        }
        $bizid = $this->bizid;
        $push_key = $this->push_key;
        $time = date('Y-m-d H:i:s', time() + (24 * 3600));
        $tx_time = strtoupper(base_convert(strtotime($time), 10, 16));
        $livecode = $bizid . "_" . $stream_id; //直播码
        $tx_secret = md5($push_key . $livecode . $tx_time);

        //区分是主播推的流还是 连麦的观众推的流  1主播推的  0观众推的
        $type = (int)$type;
        if ($type == 1) {
            //直播
            $url = 'rtmp://' . $this->push_url.'/live/' . $livecode . '?bizid=' . $bizid . '&txSecret=' . $tx_secret . '&txTime=' . $tx_time . '&live_id=' . $live_id . '&type=' . $type;
        } else {
            //连麦
            $url = 'rtmp://' . $this->push_url.'/live/' . $livecode . '?bizid=' . $bizid . '&txSecret=' . $tx_secret . '&txTime=' . $tx_time . '&link_id=' . $live_id . '&type=' . $type;
        }
        return $url;
    }

    /**
     * 拉流url
     * param $is_watch 是否获取直播分享的观看地址
     */
    public function playUrl($stream_id, $is_watch = false, $type = 1)
    {
        if (!$stream_id) {
            return false;
        }
        $bizid = $this->bizid;
        if ($is_watch) {
            $url = 'http://' . $this->play_url.'/live/' . $bizid . '_' . $stream_id . '.m3u8';
        } else {
            //type 1直播 0连麦
            if ($type) {
                $url = 'rtmp://' . $this->play_url.'/live/' . $bizid . '_' . $stream_id;
            } else {
                $bizid = $this->bizid;
                $push_key = $this->push_key;
                $time = date('Y-m-d H:i:s', time() + (24 * 3600));
                $tx_time = strtoupper(base_convert(strtotime($time), 10, 16));
                $livecode = $bizid . "_" . $stream_id; //直播码
                $tx_secret = md5($push_key . $livecode . $tx_time);
                $url = 'rtmp://' . $this->play_url.'/live/' . $bizid . '_' . $stream_id . '?bizid=' . $bizid . '&txSecret=' . $tx_secret . '&txTime=' . $tx_time;
            }
        }

        return $url;
    }


    /**
     * 查询直播状态
     * @param $stream_id
     */
    public function liveStatus($stream_id) {
        $t = time() + 60;
        $sign = md5($this->api_key . $t);
        $url = 'http://fcgi.video.qcloud.com/common_access?appid=' . $this->appid . '&interface=Live_Channel_GetStatus&Param.s.channel_id=' . $this->bizid . '_' . $stream_id . '&t=' . $t . '&sign=' . $sign;
        $curl_res = curl($url);
        $res = json_decode($curl_res, true);
        if ($res['retcode'] == 0 && isset($res['output'][0]['status']) && ($res['output'][0]['status'] == 3 || $res['output'][0]['status'] == 0)) {
            return true;
        }
        return false;
    }

    /**
     * 通知云端混流
     * param $mix_stream_session_id  #标识一次网络请求
     * param $output_stream_id  # 填输出流 id
     * param $input_stream_id  # 流ID
     * param $output_stream_bitrate  # 输出码率
     * param $image_layer  # 图层号，背景填1
     * param $image_width  # 画面宽度
     * param $image_height  # 画面高度
     * param $location_x  # x偏移：相对于背景画面左上角的横向偏移
     * param $location_y  # y偏移：相对于背景画面左上角的纵向偏移
     * create by: liuqi
     */
    public function liveMix($mix_session_id, $output_stream_id, $output_stream_bitrate = 0, $list = [])
    {
        $t = time() + 60;
        $sign = md5($this->api_key . $t);
        $url = 'http://fcgi.video.qcloud.com/common_access?appid=' . $this->appid . '&interface=Mix_StreamV2&t=' . $t . '&sign=' . $sign;
        $params = [
            'timestamp' => time(),                                                      # UNIX 时间戳数
            'eventId'   => time(),                                                      # 取随机数即可，标识一次网络请求
            'interface' => [
                'interfaceName' => 'Mix_StreamV2',                                      # 固定值
                'para'          => [
                    'app_id'                => $this->appid,                            # 直播 APPID
                    'interface'             => 'mix_streamv2.start_mix_stream_advanced',# 固定值
                    'mix_stream_session_id' => $mix_session_id,                         # 标识一次网络请求
                    'output_stream_id'      => $output_stream_id,                       # 填输出流 id
                    'output_stream_type'    => 0,                                       # 填输出流类型
                    'output_stream_bitrate' => $output_stream_bitrate,                  # 输出码率
                    'input_stream_list'     => $list
                ],
            ],
        ];
        if (!$output_stream_bitrate) {
            unset($params['interface']['para']['output_stream_bitrate']);
        }
        $curl_res = curl($url, json_encode($params), 1);
        $res = json_decode($curl_res, true);

        return $res;
    }

    /**
     * 取消混流
     * param $mix_stream_session_id # 标识一次网络请求
     * param $output_stream_id  # 输出流id
     * param $input_stream_id  # 流id
     * create by: liuqi
     */
    public function cancelMix($mix_session_id, $output_stream_id)
    {
        $t = time() + 60;
        $sign = md5($this->api_key . $t);
        $url = 'http://fcgi.video.qcloud.com/common_access?appid=' . $this->appid . '&interface=Mix_StreamV2&t=' . $t . '&sign=' . $sign;
        $params = [
            'timestamp' => time(),                     # UNIX 时间戳数
            'eventId'   => time(),
            'interface' => [
                'interfaceName' => 'Mix_StreamV2',     # 固定取值"Mix_StreamV2"
                'para'          => [
                    'app_id'                => $this->appid,
                    'interface'             => 'mix_streamv2.cancel_mix_stream',
                    'mix_stream_session_id' => $mix_session_id,
                    'output_stream_id'      => $output_stream_id,
                ],
            ],
        ];
        $curl_res = curl($url, json_encode($params), 1);
        $res = json_decode($curl_res, true);

        return $res;
    }

}