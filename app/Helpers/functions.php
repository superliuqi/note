<?php
    /**
     * Created by PhpStorm.
     * User: wanghui
     * Date: 2018/4/1
     * Time: 下午4:03
     */

    /**
     * 后台返回错误提示信息
     * @param string $data 错误信息
     * @param int $code 状态码
     * return json
     */
    function res_error($data = '', $code = '10000')
    {
        if (is_array($data)) {
            $res = [
                'code' => isset($data['code']) ? $data['code'] : $code,
                'msg'  => isset($data['msg']) ? $data['msg'] : $code,
            ];
        } else {
            $res = ['msg' => $data, 'code' => $code];
        }

        return response()->json($res, 200, [], JSON_UNESCAPED_UNICODE);
    }


    /**
     * 后台返回成功信息
     * @param string $data 错误信息
     * @param int $code 状态码
     * return json
     */
    function res_success($data = '', $count = 0, $msg = '')
    {
        $res = [
            'code'  => '0',
            'data'  => $data,
            'msg'   => $msg,
            'count' => $count,
        ];

        return response()->json($res, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取格式化的时间（默认当前时间）
     * @param int $time 时间戳
     * return json
     */
    function get_date($time = 0)
    {
        if (!$time) {
            $time = time();
        }

        return date('Y-m-d H:i:s', $time);
    }


    /**
     * redis存贮数组类型
     * @return mixed
     */
    function set_redis_array($key, $data, $time = false)
    {
        if ($key && $data) {
            \Illuminate\Support\Facades\Redis::set($key, json_encode($data));
        }
        $time = (int)$time;
        if ($time) {
            \Illuminate\Support\Facades\Redis::expire($key, $time);
        }
    }

    /**
     * redis获取数组类型
     * @return mixed
     */
    function get_redis_array($key)
    {
        if ($key) {
            $data = \Illuminate\Support\Facades\Redis::get($key);

            return json_decode($data, true);
        }

        return false;
    }

    /**
     * 获取app后台配置信息
     * @return mixed
     */
    function get_app_config($key)
    {
        $config = get_redis_array('app_config:' . config('app.key'));
        if (isset($config[$key])) {
            return $config[$key];
        }

        return false;
    }

    /**
     * @param $url 请求网址
     * @param bool $params 请求参数
     * @param int $ispost 请求方式
     * @param int $https https协议
     * @return bool|mixed
     */
    function curl($url, $params = false, $ispost = 0, $https = 0)
    {
        $httpInfo = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        }
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                if (is_array($params)) {
                    $params = http_build_query($params);
                }
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $response = curl_exec($ch);

        if ($response === false) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);

        return $response;
    }

    /**
     * 获取本月一头一尾日期
     * $now ='2018-09-12';
     */
    function getMonth($date)
    {
        $firstday = date("Y-m-01", strtotime($date));
        $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));

        return [$firstday, $lastday];
    }


    /**
     * 本周的第一天和最后一天
     * $now =时间戳;
     */
    function getWeek($now)
    {
        $time = ('1' == date('w')) ? strtotime('Monday', $now) : strtotime('last Monday', $now);

        //下面2句就是将上面得到的时间做一个起止转换

        //得到本周开始的时间，时间格式为：yyyy-mm-dd hh:ii:ss 的格式
        $beginTime = date('Y-m-d', $time);

        //得到本周末最后的时间
        $endTime = date('Y-m-d', strtotime('Sunday', $now));

        return [$beginTime, $endTime];
    }


    /**
     * 获取当前日期前30天
     * $now =时间戳;
     */
    function getLeftDay()
    {
        $days = []; #首先定义一个空数组

        for ($i = 0; $i < 30; $i ++) {
            $days[] = date("Y-m-d", strtotime(' -' . $i . 'day'));

        }
        $etimestamp= (current($days));
        $stimestamp = (end($days));
        $nowdays=[$stimestamp,$etimestamp];
        return $nowdays;
    }


    /**
     * 获取指定日期段内每一天的日期
     * @param  Date $startdate 开始日期
     * @param  Date $enddate 结束日期
     * $date=['2018-09-10','2018-09-12']
     * @return Array
     */
    function getDateFromRange($date)
    {


        $stimestamp = strtotime(current($date));
        $etimestamp = strtotime(end($date));


        // 计算日期段内有多少天
        $days = ($etimestamp - $stimestamp) / 86400 + 1;

        // 保存每天日期
        $date = [];

        for ($i = 0; $i < $days; $i ++) {
            $date[] = date('Y-m-d', $stimestamp + (86400 * $i));
        }

        return $date;
    }

    /** * 获取本周所有日期 */
    function get_week($time = '', $format = 'Y-m-d')
    {
        $time = $time != '' ? $time : time();  //获取当前周几
        $week = date('w', $time);
        $date = [];
        for ($i = 1; $i <= 7; $i ++) {
            $date[$i] = date($format, strtotime('+' . $i - $week . ' days', $time));
        }

        return $date;
    }


    /**
     * sql debug输出
     */
    function get_sql_debug()
    {
        \DB::listen(function ($sql) {
            dump($sql);
            $singleSql = $sql->sql;
            if ($sql->bindings) {
                foreach ($sql->bindings as $replace) {
                    $value = is_numeric($replace) ? $replace : "'" . $replace . "'";
                    $singleSql = preg_replace('/\?/', $value, $singleSql, 1);
                }
                dump($singleSql);
            } else {
                dump($singleSql);
            }
        });


    }




