<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\Order;
use App\Jobs\ProcessPodcast;
use Illuminate\Http\Request;

class IndexController extends Controller
{

    public function index(Request $request)
    {
        $cityName = $request->cityName;

        $result = file_get_contents('http://api.map.baidu.com/geocoder?address=' . $cityName . '&output=json&key=37492c0ee6f924cb5e934fa08c6b1676&city=%E5%8C%97%E4%BA%AC%E5%B8%82');
        return $result;
    }

    public function delay()
    {
        $id = mt_rand(1,100);
        ProcessPodcast::dispatch(time())->delay(now()->addMinutes(mt_rand(1,3)));
        Order::dispatch(['start_at'=>time(),'id'=>$id])->onQueue('order')->delay(now()->addMinutes(mt_rand(1,3)));
        echo $id.'<br/>';
        echo time();
    }

}